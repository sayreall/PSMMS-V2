<?php

namespace App\Controllers\Users\MsaPartners;

use App\Config\App;
use App\Controllers\BaseController;
use App\Config\Database;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\Users\MsaPartners\MsaPartnerModel;

class PartnersController extends BaseController
{
    private MsaPartnerModel $msaPartnerModel;

    public function __construct()
    {
        $this->msaPartnerModel = new MsaPartnerModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $db = Database::getInstance();
        $sql = "
            SELECT *
            FROM (
                SELECT
                    mp.id AS row_id,
                    'partner' AS source_type,
                    mp.id,
                    mp.company_name,
                    mp.username,
                    mp.contact_no,
                    mp.address,
                    mp.installer,
                    mp.msa_type,
                    mp.email,
                    mp.profile_picture,
                    mp.status,
                    mp.created_at
                FROM msa_partners mp

                UNION ALL

                SELECT
                    u.id AS row_id,
                    'user' AS source_type,
                    u.id,
                    COALESCE(u.name, '') AS company_name,
                    COALESCE(u.name, '') AS username,
                    COALESCE(u.contact_no, '') AS contact_no,
                    '' AS address,
                    '' AS installer,
                    '' AS msa_type,
                    COALESCE(u.email, '') AS email,
                    u.avatar AS profile_picture,
                    u.status,
                    u.created_at
                FROM users u
                WHERE u.role = 'msa_partners'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM msa_partners mx
                      WHERE (mx.user_id IS NOT NULL AND mx.user_id = u.id)
                         OR (mx.email IS NOT NULL AND mx.email = u.email)
                  )
            ) AS combined_partners
            ORDER BY created_at DESC, row_id DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $partners = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('msa_partners.partners.index', [
            'title' => 'Partners',
            'partners' => $partners,
        ]);
    }


    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $company_name = trim($data['company_name'] ?? '');
        $username = trim($data['username'] ?? '');
        $contact_no = trim($data['contact_no'] ?? '');
        $address = trim($data['address'] ?? '');
        $installer = trim($data['installer'] ?? '');
        $msa_type = trim($data['msa_type'] ?? '');
        $email = trim($data['email'] ?? '');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'company_name' => 'required|min:2|max:150',
            'username' => 'required|min:2|max:100|unique:msa_partners.username',
            'contact_no' => 'required|regex:/^\d{11}$/',
            'address' => 'required|min:5|max:255',
            'installer' => 'required|min:2|max:150',
            'msa_type' => 'required|in:regional,ncr',
            'email' => 'required|email|unique:msa_partners.email',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('company_name', 'username', 'contact_no', 'address', 'installer', 'msa_type', 'email', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add MSA partner. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $profilePicturePath = null;
        if (isset($_FILES['profile_picture']) && ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $profilePicturePath = Upload::store($_FILES['profile_picture'], 'msa_partners', 'msa_');
            if ($profilePicturePath === null) {
                Session::flash('message', 'Profile picture upload failed. Please use a valid image file (max 5MB).');
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $this->msaPartnerModel->createMsaPartner([
            'user_id' => null,
            'company_name' => $company_name,
            'username' => $username,
            'contact_no' => $contact_no,
            'address' => $address,
            'installer' => $installer,
            'msa_type' => $msa_type,
            'email' => $email,
            'profile_picture' => $profilePicturePath,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'msa_partner_create', "Created MSA partner: {$company_name}");
        Session::flash('message', 'MSA partner added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('partners'));
    }

    public function approve(string $source, int $id): void
    {
        Csrf::verify();

        $db = Database::getInstance();
        $updated = false;

        if ($source === 'partner') {
            $fetch = $db->prepare("SELECT id, user_id, email, status FROM msa_partners WHERE id = :id LIMIT 1");
            $fetch->execute([':id' => $id]);
            $partner = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$partner) {
                Session::flash('message', 'MSA partner not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('partners'));
            }

            $stmt = $db->prepare("UPDATE msa_partners SET status = 'active', updated_at = :updated_at WHERE id = :id AND status = 'pending'");
            $stmt->execute([':id' => $id, ':updated_at' => date('Y-m-d H:i:s')]);
            $updated = $stmt->rowCount() > 0;

            $linkedUserId = (int)($partner['user_id'] ?? 0);
            if ($linkedUserId > 0) {
                $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE id = :id AND role = 'msa_partners' AND status = 'pending'")
                    ->execute([':id' => $linkedUserId, ':updated_at' => date('Y-m-d H:i:s')]);
            } elseif (!empty($partner['email'])) {
                $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE role = 'msa_partners' AND email = :email AND status = 'pending'")
                    ->execute([':email' => $partner['email'], ':updated_at' => date('Y-m-d H:i:s')]);
            }
        } elseif ($source === 'user') {
            $fetch = $db->prepare("SELECT id, email, status FROM users WHERE id = :id AND role = 'msa_partners' LIMIT 1");
            $fetch->execute([':id' => $id]);
            $user = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$user) {
                Session::flash('message', 'MSA user not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('partners'));
            }

            $stmt = $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE id = :id AND role = 'msa_partners' AND status = 'pending'");
            $stmt->execute([':id' => $id, ':updated_at' => date('Y-m-d H:i:s')]);
            $updated = $stmt->rowCount() > 0;

            if (!empty($user['email'])) {
                $db->prepare("UPDATE msa_partners SET status = 'active', updated_at = :updated_at WHERE email = :email AND status = 'pending'")
                    ->execute([':email' => $user['email'], ':updated_at' => date('Y-m-d H:i:s')]);
            }
        } else {
            Session::flash('message', 'Invalid partner source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('partners'));
        }

        Session::flash('message', $updated ? 'MSA partner approved successfully.' : 'MSA partner is already approved or inactive.');
        Session::flash('message_type', $updated ? 'success' : 'info');
        $this->redirect(App::url('partners'));
    }

    public function delete(string $source, int $id): void
    {
        Csrf::verify();

        $db = Database::getInstance();
        $deleted = false;

        if ($source === 'partner') {
            $stmt = $db->prepare("DELETE FROM msa_partners WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $deleted = $stmt->rowCount() > 0;
        } elseif ($source === 'user') {
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id AND role = 'msa_partners' LIMIT 1");
            $stmt->execute([':id' => $id]);
            $deleted = $stmt->rowCount() > 0;
        } else {
            Session::flash('message', 'Invalid partner source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('partners'));
        }

        Session::flash('message', $deleted ? 'MSA partner deleted successfully.' : 'No partner record was deleted.');
        Session::flash('message_type', $deleted ? 'success' : 'info');
        $this->redirect(App::url('partners'));
    }
}
