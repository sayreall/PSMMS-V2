<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Mailer;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\ManagerModel;
use App\Models\UserModel;

class ManagersController extends BaseController
{
    private ManagerModel $managerModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->managerModel = new ManagerModel();
        $this->userModel = new UserModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $db = \App\Config\Database::getInstance();
        $sql = "
            SELECT *
            FROM (
                SELECT
                    m.id AS row_id,
                    'manager' AS source_type,
                    m.id,
                    m.manager_name,
                    m.position,
                    m.contact_no,
                    m.company_email,
                    m.email,
                    m.profile_picture,
                    m.status,
                    m.created_at
                FROM managers m

                UNION ALL

                SELECT
                    u.id AS row_id,
                    'user' AS source_type,
                    u.id,
                    u.name AS manager_name,
                    u.role AS position,
                    COALESCE(u.contact_no, '') AS contact_no,
                    u.company_email,
                    u.email,
                    u.avatar AS profile_picture,
                    u.status,
                    u.created_at
                FROM users u
                WHERE u.role = 'asm_manager'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM managers mx
                      WHERE (mx.email IS NOT NULL AND mx.email = u.email)
                         OR (mx.company_email IS NOT NULL AND mx.company_email = u.company_email)
                  )
            ) AS combined_managers
            ORDER BY created_at DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $managers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('managers.index', [
            'title' => 'Managers',
            'managers' => $managers,
        ]);
    }


    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();

        $manager_name = trim($data['manager_name'] ?? '');
        $position = trim($data['position'] ?? '');
        $contact_no = trim($data['contact_no'] ?? '');
        $company_email = trim($data['company_email'] ?? '');
        $email = trim($data['email'] ?? '');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'manager_name' => 'required|min:2|max:150',
            'position' => 'required|max:100',
            'contact_no' => 'required|max:30',
            'company_email' => 'required|email|unique:managers.company_email',
            'email' => 'required|email|unique:managers.email',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('manager_name', 'position', 'contact_no', 'company_email', 'email', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add manager. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $profilePicturePath = null;
        if (isset($_FILES['profile_picture']) && ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $profilePicturePath = Upload::store($_FILES['profile_picture'], 'managers', 'mgr_');
            if ($profilePicturePath === null) {
                Session::flash('message', 'Profile picture upload failed. Please use a valid image file (max 5MB).');
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $db = \App\Config\Database::getInstance();
        $userLookup = $db->prepare("SELECT id, email, company_email FROM users WHERE email = :email OR company_email = :company_email LIMIT 1");
        $userLookup->execute([
            ':email' => $email,
            ':company_email' => $company_email,
        ]);
        $existingUser = $userLookup->fetch(\PDO::FETCH_ASSOC) ?: null;

        $linkedUserId = null;
        $generatedPassword = null;
        $mailError = null;
        $credentialEmailSent = false;

        if ($existingUser) {
            $linkedUserId = (int)($existingUser['id'] ?? 0);
        } else {
            $generatedPassword = $this->generateTemporaryPassword();
            $hashedPassword = password_hash($generatedPassword, PASSWORD_BCRYPT, ['cost' => 12]);

            $linkedUserId = $this->userModel->createUser([
                'name' => $manager_name,
                'email' => $email,
                'company_email' => $company_email,
                'contact_no' => $contact_no ?: null,
                'password' => $hashedPassword,
                'role' => 'asm_manager',
                'status' => 'active',
            ]);

            $credentialEmailSent = Mailer::send(
                $email,
                $manager_name,
                'Your PSMMS account credentials',
                '<p>Hello ' . htmlspecialchars($manager_name) . ',</p>'
                . '<p>A manager account has been created for you by Super Admin.</p>'
                . '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>'
                . '<p style="font-size:16px;"><strong>Temporary Password:</strong></p>'
                . '<p style="font-size:20px;font-weight:700;letter-spacing:.5px;padding:8px 12px;border:1px solid #e2e8f0;background:#f8fafc;display:inline-block;">' . htmlspecialchars($generatedPassword) . '</p>'
                . '<p>Please sign in and change your password immediately.</p>'
                . '<p>Regards,<br>PSMMS Team</p>',
                $mailError
            );
        }

        $this->managerModel->createManager([
            'user_id' => $linkedUserId ?: null,
            'manager_name' => $manager_name,
            'position' => $position,
            'contact_no' => $contact_no ?: null,
            'company_email' => $company_email,
            'email' => $email,
            'profile_picture' => $profilePicturePath,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'manager_create', "Created manager: {$manager_name}");

        if ($generatedPassword !== null) {
            if ($credentialEmailSent) {
                Session::flash('message', 'Manager added and login credentials emailed successfully.');
                Session::flash('message_type', 'success');
            } else {
                if (!empty($mailError)) {
                    error_log('Manager credential email send failed for manager ' . $manager_name . ': ' . $mailError);
                }
                Session::flash('message', 'Manager added, but credential email failed to send.');
                Session::flash('message_type', 'warning');
            }
        } else {
            Session::flash('message', 'Manager added successfully.');
            Session::flash('message_type', 'success');
        }

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('managers')]);
        }

        $this->redirect(App::url('managers'));
    }

    private function generateTemporaryPassword(int $length = 12): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        $max = strlen($alphabet) - 1;
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $alphabet[random_int(0, $max)];
        }

        return $password;
    }

    public function approve(string $source, int $id): void
    {
        Csrf::verify();

        $db = \App\Config\Database::getInstance();
        $recipientEmail = '';
        $recipientName = 'User';

        if ($source === 'manager') {
            $fetch = $db->prepare("SELECT manager_name, email, status FROM managers WHERE id = :id LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: [];
            $recipientEmail = (string)($row['email'] ?? '');
            $recipientName = (string)($row['manager_name'] ?? 'Manager');

            $stmt = $db->prepare("UPDATE managers SET status = 'active', updated_at = :updated_at WHERE id = :id AND status = 'pending'");
            $stmt->execute([
                ':id' => $id,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);
        } elseif ($source === 'user') {
            $fetch = $db->prepare("SELECT name, email, status FROM users WHERE id = :id AND role = 'asm_manager' LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: [];
            $recipientEmail = (string)($row['email'] ?? '');
            $recipientName = (string)($row['name'] ?? 'User');

            $stmt = $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE id = :id AND role = 'asm_manager' AND status = 'pending'");
            $stmt->execute([
                ':id' => $id,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            Session::flash('message', 'Invalid manager source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('managers'));
        }

        (new ActivityLogModel())->log(Auth::id(), 'manager_approve', "Approved manager {$source} ID: {$id}");

        $mailError = null;
        $emailSent = false;
        if ($recipientEmail !== '') {
            $emailSent = Mailer::send(
                $recipientEmail,
                $recipientName,
                'Your account is now active',
                '<p>Hello ' . htmlspecialchars($recipientName) . ',</p>'
                . '<p>Your account has been approved and is now ready to use.</p>'
                . '<p>You may now sign in to the PSMMS dashboard.</p>'
                . '<p>Regards,<br>PSMMS Team</p>',
                $mailError
            );
        }

        if ($emailSent) {
            Session::flash('message', 'Manager approved and email notification sent.');
            Session::flash('message_type', 'success');
        } else {
            if (!empty($mailError)) {
                error_log('Manager approval email send failed for ' . $source . ' ID ' . $id . ': ' . $mailError);
            }
            Session::flash('message', 'Manager approved, but email notification failed to send.');
            Session::flash('message_type', 'warning');
        }

        $this->redirect(App::url('managers'));
    }
}
