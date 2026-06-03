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
        $asmManagers = $this->getSalesManagerOptions();
        $salesCategoryOptions = ['surf2sawa', 'fiberx', 'bida', 'sme'];
        $sql = "
            SELECT *
            FROM (
                SELECT
                    mp.id AS row_id,
                    'partner' AS source_type,
                    mp.id,
                    mp.partners_id,
                    COALESCE(NULLIF(mp.user_type, ''), 'msa_partners') AS user_type,
                    mp.sales_manager,
                    mp.company_name,
                    mp.first_name,
                    mp.middle_name,
                    mp.last_name,
                    mp.contact,
                    mp.email,
                    mp.password,
                    mp.photos,
                    mp.area_type,
                    mp.address,
                    mp.sales_category,
                    mp.status,
                    mp.created_at
                FROM msa_partners mp

                UNION ALL

                SELECT
                    u.id AS row_id,
                    'user' AS source_type,
                    u.id,
                    u.id AS partners_id,
                    u.role AS user_type,
                    '' AS sales_manager,
                    COALESCE(u.name, '') AS company_name,
                    COALESCE(u.first_name, '') AS first_name,
                    COALESCE(u.middle_name, '') AS middle_name,
                    COALESCE(u.last_name, '') AS last_name,
                    COALESCE(u.contact_no, '') AS contact,
                    COALESCE(u.email, '') AS email,
                    u.password,
                    u.avatar AS photos,
                    '' AS area_type,
                    '' AS address,
                    '' AS sales_category,
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
            'asmManagers' => $asmManagers,
            'salesCategoryOptions' => $salesCategoryOptions,
        ]);
    }


    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $sales_manager = trim($data['sales_manager'] ?? '');
        $company_name = trim($data['company_name'] ?? '');
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $contact = trim($data['contact'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim((string)($data['password'] ?? ''));
        $area_type = trim($data['area_type'] ?? '');
        $address = trim($data['address'] ?? '');
        $sales_category = trim($data['sales_category'] ?? '');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'sales_manager' => 'required|max:150',
            'company_name' => 'required|min:2|max:150',
            'first_name' => 'required|min:2|max:100',
            'middle_name' => 'max:100',
            'last_name' => 'required|min:2|max:100',
            'contact' => 'required|regex:/^\d{11}$/',
            'email' => 'required|email|unique:msa_partners.email',
            'password' => $password !== '' ? 'min:8' : 'max:255',
            'area_type' => 'required|in:regional,ncr',
            'address' => 'required|min:5|max:255',
            'sales_category' => 'required|in:surf2sawa,fiberx,bida,sme',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('sales_manager', 'company_name', 'first_name', 'middle_name', 'last_name', 'contact', 'email', 'password', 'area_type', 'address', 'sales_category', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add MSA partner. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $photos = null;
        if (isset($_FILES['photos']) && ($_FILES['photos']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photos = Upload::store($_FILES['photos'], 'msa_partners', 'msa_');
            if ($photos === null) {
                Session::flash('message', 'Photo upload failed. Please use a valid image file (max 5MB).');
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $this->msaPartnerModel->createMsaPartner([
            'user_id' => null,
            'user_type' => 'msa_partners',
            'sales_manager' => $sales_manager,
            'company_name' => $company_name,
            'first_name' => $first_name,
            'middle_name' => $middle_name ?: null,
            'last_name' => $last_name,
            'contact' => $contact,
            'email' => $email,
            'password' => $password !== '' ? password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) : null,
            'photos' => $photos,
            'area_type' => $area_type,
            'address' => $address,
            'sales_category' => $sales_category,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'msa_partner_create', "Created MSA partner: {$company_name}");
        Session::flash('message', 'MSA partner added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('partners'));
    }

    public function update(string $source, int $id): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $sales_manager = trim($data['sales_manager'] ?? '');
        $company_name = trim($data['company_name'] ?? '');
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $contact = trim($data['contact'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim((string)($data['password'] ?? ''));
        $area_type = trim($data['area_type'] ?? '');
        $address = trim($data['address'] ?? '');
        $sales_category = trim($data['sales_category'] ?? '');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'sales_manager' => 'required|max:150',
            'company_name' => 'required|min:2|max:150',
            'first_name' => 'required|min:2|max:100',
            'middle_name' => 'max:100',
            'last_name' => 'required|min:2|max:100',
            'contact' => 'required|regex:/^\d{11}$/',
            'email' => 'required|email',
            'password' => $password !== '' ? 'min:8' : 'max:255',
            'area_type' => 'required|in:regional,ncr',
            'address' => 'required|min:5|max:255',
            'sales_category' => 'required|in:surf2sawa,fiberx,bida,sme',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('sales_manager', 'company_name', 'first_name', 'middle_name', 'last_name', 'contact', 'email', 'password', 'area_type', 'address', 'sales_category', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('message', 'Unable to update MSA partner. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $db = Database::getInstance();
        $hashedPassword = $password !== '' ? password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) : null;
        $photos = null;

        if (isset($_FILES['photos']) && ($_FILES['photos']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photos = Upload::store($_FILES['photos'], 'msa_partners', 'msa_');
            if ($photos === null) {
                $errors = ['photos' => ['Photo upload failed. Please use a valid image file (max 5MB).']];
                if (App::isAjax() || App::isApiRequest()) {
                    Validation::jsonResponse($errors);
                }
                Session::flash('message', $errors['photos'][0]);
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $linkedUserId = 0;
        $now = date('Y-m-d H:i:s');

        if ($source === 'partner') {
            $fetch = $db->prepare("SELECT id, user_id FROM msa_partners WHERE id = :id LIMIT 1");
            $fetch->execute([':id' => $id]);
            $partner = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$partner) {
                $this->respondPartnerUpdateNotFound();
            }

            $linkedUserId = (int)($partner['user_id'] ?? 0);
            $payload = [
                ':id' => $id,
                ':sales_manager' => $sales_manager,
                ':company_name' => $company_name,
                ':first_name' => $first_name,
                ':middle_name' => $middle_name !== '' ? $middle_name : null,
                ':last_name' => $last_name,
                ':contact' => $contact,
                ':email' => $email,
                ':area_type' => $area_type,
                ':address' => $address,
                ':sales_category' => $sales_category,
                ':status' => $status,
                ':updated_at' => $now,
            ];

            $setPassword = '';
            if ($hashedPassword !== null) {
                $setPassword = ', password = :password';
                $payload[':password'] = $hashedPassword;
            }

            $setPhotos = '';
            if ($photos !== null) {
                $setPhotos = ', photos = :photos';
                $payload[':photos'] = $photos;
            }

            $stmt = $db->prepare("
                UPDATE msa_partners
                SET
                    user_type = 'msa_partners',
                    sales_manager = :sales_manager,
                    company_name = :company_name,
                    first_name = :first_name,
                    middle_name = :middle_name,
                    last_name = :last_name,
                    contact = :contact,
                    email = :email,
                    area_type = :area_type,
                    address = :address,
                    sales_category = :sales_category,
                    status = :status,
                    updated_at = :updated_at
                    {$setPassword}
                    {$setPhotos}
                WHERE id = :id
                LIMIT 1
            ");
            $stmt->execute($payload);
        } elseif ($source === 'user') {
            $fetch = $db->prepare("SELECT id, password FROM users WHERE id = :id AND role = 'msa_partners' LIMIT 1");
            $fetch->execute([':id' => $id]);
            $user = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$user) {
                $this->respondPartnerUpdateNotFound();
            }

            $linkedUserId = $id;
            $this->msaPartnerModel->createMsaPartner([
                'user_id' => $id,
                'user_type' => 'msa_partners',
                'sales_manager' => $sales_manager,
                'company_name' => $company_name,
                'first_name' => $first_name,
                'middle_name' => $middle_name ?: null,
                'last_name' => $last_name,
                'contact' => $contact,
                'email' => $email,
                'password' => $hashedPassword ?? ($user['password'] ?? null),
                'photos' => $photos,
                'area_type' => $area_type,
                'address' => $address,
                'sales_category' => $sales_category,
                'status' => $status,
            ]);
        } else {
            $errors = ['source' => ['Invalid partner source.']];
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($errors, 422);
            }
            Session::flash('message', 'Invalid partner source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('partners'));
        }

        if ($linkedUserId > 0) {
            $this->syncLinkedUserProfile($linkedUserId, [
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'email' => $email,
                'contact' => $contact,
                'status' => $status,
                'password' => $hashedPassword,
                'photos' => $photos,
            ]);
        }

        (new ActivityLogModel())->log(Auth::id(), 'msa_partner_update', "Updated MSA partner {$source} ID: {$id}");
        Session::flash('message', 'MSA partner updated successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('partners')]);
        }

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

    private function getSalesManagerOptions(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT DISTINCT sm.manager_name
            FROM (
                SELECT TRIM(COALESCE(m.manager_name, '')) AS manager_name
                FROM managers m
                WHERE LOWER(TRIM(COALESCE(m.position, ''))) IN ('asm_manager', 'area_sales_manager')

                UNION ALL

                SELECT TRIM(
                    COALESCE(
                        NULLIF(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')), ' '),
                        u.name,
                        ''
                    )
                ) AS manager_name
                FROM users u
                WHERE LOWER(TRIM(COALESCE(u.role, ''))) = 'asm_manager'
            ) sm
            WHERE sm.manager_name <> ''
            ORDER BY sm.manager_name ASC
        ");
        $stmt->execute();

        return array_values(array_filter(array_map(
            static fn(array $row): string => trim((string)($row['manager_name'] ?? '')),
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        )));
    }

    private function syncLinkedUserProfile(int $userId, array $data): void
    {
        $db = Database::getInstance();
        $first = trim((string)($data['first_name'] ?? ''));
        $middle = trim((string)($data['middle_name'] ?? ''));
        $last = trim((string)($data['last_name'] ?? ''));
        $name = trim(preg_replace('/\s+/', ' ', trim("$first $middle $last")) ?? '');

        $payload = [
            ':id' => $userId,
            ':name' => $name,
            ':first_name' => $first,
            ':middle_name' => $middle !== '' ? $middle : null,
            ':last_name' => $last,
            ':email' => trim((string)($data['email'] ?? '')),
            ':contact_no' => trim((string)($data['contact'] ?? '')),
            ':status' => trim((string)($data['status'] ?? 'active')),
            ':updated_at' => date('Y-m-d H:i:s'),
        ];

        $setPassword = '';
        if (!empty($data['password'])) {
            $setPassword = ', password = :password';
            $payload[':password'] = $data['password'];
        }

        $setAvatar = '';
        if (!empty($data['photos'])) {
            $setAvatar = ', avatar = :avatar';
            $payload[':avatar'] = $data['photos'];
        }

        $stmt = $db->prepare("
            UPDATE users
            SET
                name = :name,
                first_name = :first_name,
                middle_name = :middle_name,
                last_name = :last_name,
                email = :email,
                contact_no = :contact_no,
                status = :status,
                updated_at = :updated_at
                {$setPassword}
                {$setAvatar}
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute($payload);
    }

    private function respondPartnerUpdateNotFound(): void
    {
        if (App::isAjax() || App::isApiRequest()) {
            Validation::jsonResponse(['id' => ['MSA partner not found.']], 404);
        }

        Session::flash('message', 'MSA partner not found.');
        Session::flash('message_type', 'error');
        $this->redirect(App::url('partners'));
    }
}
