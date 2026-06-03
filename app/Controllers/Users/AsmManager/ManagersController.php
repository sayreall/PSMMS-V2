<?php

namespace App\Controllers\Users\AsmManager;

use App\Config\App;
use App\Config\Database;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Mailer;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\Users\AsmManager\ManagerModel;
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
                    m.manager_id,
                    m.user_type,
                    m.manager_name,
                    m.first_name,
                    m.middle_name,
                    m.last_name,
                    m.sales_manager,
                    m.position,
                    m.contact_no,
                    m.contact,
                    m.company_email,
                    m.password,
                    m.email,
                    m.photos,
                    m.status,
                    m.created_at
                FROM managers m

                UNION ALL

                SELECT
                    u.id AS row_id,
                    'user' AS source_type,
                    u.id,
                    u.id AS manager_id,
                    u.role AS user_type,
                    u.name AS manager_name,
                    u.first_name,
                    u.middle_name,
                    u.last_name,
                    u.name AS sales_manager,
                    u.role AS position,
                    COALESCE(u.contact_no, '') AS contact_no,
                    COALESCE(u.contact_no, '') AS contact,
                    u.company_email,
                    u.password,
                    u.email,
                    u.avatar AS photos,
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

        return $this->render('asm_manager.managers.index', [
            'title' => 'Managers',
            'managers' => $managers,
        ]);
    }


    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();

        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $manager_name = trim($data['manager_name'] ?? '');
        if ($manager_name === '') {
            $manager_name = $this->composeFullName($first_name, $middle_name, $last_name);
        }
        $sales_manager = trim($data['sales_manager'] ?? '');
        if ($sales_manager === '') {
            $sales_manager = $manager_name;
        }
        $position = trim($data['position'] ?? '');
        $contact_no = trim($data['contact'] ?? ($data['contact_no'] ?? ''));
        $company_email = trim($data['company_email'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim((string)($data['password'] ?? ''));
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'manager_name' => 'required|min:2|max:150',
            'first_name' => 'max:100',
            'middle_name' => 'max:100',
            'last_name' => 'max:100',
            'sales_manager' => 'max:150',
            'position' => 'required|max:100',
            'contact_no' => 'required|max:30',
            'company_email' => 'required|email|unique:managers.company_email',
            'email' => 'required|email|unique:managers.email',
            'password' => 'min:8',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('manager_name', 'first_name', 'middle_name', 'last_name', 'sales_manager', 'position', 'contact_no', 'company_email', 'email', 'password', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add manager. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $photoPath = null;
        if (isset($_FILES['photos']) && ($_FILES['photos']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photoPath = Upload::store($_FILES['photos'], 'managers', 'mgr_');
            if ($photoPath === null) {
                Session::flash('message', 'Profile picture upload failed. Please use a valid image file (max 5MB).');
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $db = \App\Config\Database::getInstance();
        $userLookup = $db->prepare("SELECT id, email, company_email, password FROM users WHERE email = :email OR company_email = :company_email LIMIT 1");
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
            $hashedPassword = (string)($existingUser['password'] ?? '');
        } else {
            $generatedPassword = $password !== '' ? null : $this->generateTemporaryPassword();
            $plainPassword = $password !== '' ? $password : $generatedPassword;
            $hashedPassword = password_hash((string)$plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);

            $linkedUserId = $this->userModel->createUser([
                'name' => $manager_name,
                'first_name' => $first_name ?: null,
                'middle_name' => $middle_name ?: null,
                'last_name' => $last_name ?: null,
                'email' => $email,
                'company_email' => $company_email,
                'contact_no' => $contact_no ?: null,
                'password' => $hashedPassword,
                'role' => 'asm_manager',
                'status' => $status,
            ]);

            if ($generatedPassword !== null) {
                $credentialEmailSent = Mailer::send(
                    $email,
                    $manager_name,
                    'Your PSMMS account credentials',
                    '<p>Hello ' . htmlspecialchars($manager_name) . ',</p>'
                    . '<p>A manager account has been created for you by Super Admin.</p>'
                    . '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>'
                    . '<p style="font-size:16px;"><strong>Temporary Password:</strong></p>'
                    . '<p style="font-size:20px;font-weight:700;letter-spacing:.5px;padding:8px 12px;border:1px solid #e2e8f0;background:#f8fafc;display:inline-block;">' . htmlspecialchars((string)$generatedPassword) . '</p>'
                    . '<p>Please sign in and change your password immediately.</p>'
                    . '<p>Regards,<br>PSMMS Team</p>',
                    $mailError
                );
            }
        }

        $this->managerModel->createManager([
            'user_id' => $linkedUserId ?: null,
            'user_type' => 'asm_manager',
            'manager_name' => $manager_name,
            'first_name' => $first_name ?: null,
            'middle_name' => $middle_name ?: null,
            'last_name' => $last_name ?: null,
            'sales_manager' => $sales_manager,
            'position' => $position,
            'contact_no' => $contact_no ?: null,
            'contact' => $contact_no ?: null,
            'company_email' => $company_email,
            'password' => isset($hashedPassword) ? $hashedPassword : null,
            'email' => $email,
            'photos' => $photoPath,
            'status' => $status,
        ]);

        if ($linkedUserId > 0) {
            $this->syncLinkedUserStatus($linkedUserId, $email, $company_email, $status);
        }

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

    private function syncLinkedUserStatus(int $userId, string $email, string $companyEmail, string $status): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE users
            SET status = :status, updated_at = :updated_at
            WHERE id = :id
               OR (:email_filter <> '' AND email = :email_value)
               OR (:company_email_filter <> '' AND company_email = :company_email_value)
        ");
        $stmt->execute([
            ':status' => $status,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $userId,
            ':email_filter' => $email,
            ':email_value' => $email,
            ':company_email_filter' => $companyEmail,
            ':company_email_value' => $companyEmail,
        ]);
    }

    public function update(string $source, int $id): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $firstName = trim($data['first_name'] ?? '');
        $middleName = trim($data['middle_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $managerName = trim($data['manager_name'] ?? '');
        if ($managerName === '') {
            $managerName = $this->composeFullName($firstName, $middleName, $lastName);
        }
        $salesManager = trim($data['sales_manager'] ?? '');
        if ($salesManager === '') {
            $salesManager = $managerName;
        }

        $payload = [
            'manager_name' => $managerName,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'sales_manager' => $salesManager,
            'position' => trim($data['position'] ?? ''),
            'contact' => trim($data['contact'] ?? ($data['contact_no'] ?? '')),
            'email' => trim($data['email'] ?? ''),
            'company_email' => trim($data['company_email'] ?? ''),
            'status' => trim($data['status'] ?? 'active'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db = Database::getInstance();

        if ($source === 'manager') {
            $existingStmt = $db->prepare("SELECT id, user_id FROM managers WHERE id = :id LIMIT 1");
            $existingStmt->execute([':id' => $id]);
            $existing = $existingStmt->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$existing) {
                Session::flash('message', 'Manager record not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('managers'));
            }

            $stmt = $db->prepare("
                UPDATE managers
                SET manager_name = :manager_name,
                    first_name = :first_name,
                    middle_name = :middle_name,
                    last_name = :last_name,
                    sales_manager = :sales_manager,
                    position = :position,
                    contact_no = :contact,
                    contact = :contact,
                    email = :email,
                    company_email = :company_email,
                    user_type = COALESCE(user_type, 'asm_manager'),
                    status = :status,
                    updated_at = :updated_at
                WHERE id = :id
                LIMIT 1
            ");
            $stmt->execute([
                ':id' => $id,
                ':manager_name' => $payload['manager_name'],
                ':first_name' => $payload['first_name'] !== '' ? $payload['first_name'] : null,
                ':middle_name' => $payload['middle_name'] !== '' ? $payload['middle_name'] : null,
                ':last_name' => $payload['last_name'] !== '' ? $payload['last_name'] : null,
                ':sales_manager' => $payload['sales_manager'] !== '' ? $payload['sales_manager'] : null,
                ':position' => $payload['position'],
                ':contact' => $payload['contact'] !== '' ? $payload['contact'] : null,
                ':email' => $payload['email'],
                ':company_email' => $payload['company_email'],
                ':status' => $payload['status'],
                ':updated_at' => $payload['updated_at'],
            ]);

            $linkedUserId = (int)($existing['user_id'] ?? 0);
            if ($linkedUserId > 0) {
                $this->syncLinkedUserProfile($linkedUserId, $payload);
            }
        } elseif ($source === 'user') {
            $this->syncLinkedUserProfile($id, $payload);
        } else {
            Session::flash('message', 'Invalid manager source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('managers'));
        }

        Session::flash('message', 'Manager updated successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('managers')]);
        }

        $this->redirect(App::url('managers'));
    }

    private function syncLinkedUserProfile(int $userId, array $payload): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE users
            SET name = :name,
                first_name = :first_name,
                middle_name = :middle_name,
                last_name = :last_name,
                email = :email,
                company_email = :company_email,
                contact_no = :contact_no,
                status = :status,
                updated_at = :updated_at
            WHERE id = :id
              AND role = 'asm_manager'
            LIMIT 1
        ");
        $stmt->execute([
            ':id' => $userId,
            ':name' => $payload['manager_name'],
            ':first_name' => $payload['first_name'] !== '' ? $payload['first_name'] : null,
            ':middle_name' => $payload['middle_name'] !== '' ? $payload['middle_name'] : null,
            ':last_name' => $payload['last_name'] !== '' ? $payload['last_name'] : null,
            ':email' => $payload['email'],
            ':company_email' => $payload['company_email'] !== '' ? $payload['company_email'] : null,
            ':contact_no' => $payload['contact'] !== '' ? $payload['contact'] : null,
            ':status' => $payload['status'],
            ':updated_at' => $payload['updated_at'],
        ]);
    }

    private function composeFullName(string $first, string $middle, string $last): string
    {
        return trim(preg_replace('/\s+/', ' ', trim("$first $middle $last")) ?? '');
    }

    public function approve(string $source, int $id): void
    {
        Csrf::verify();

        $db = \App\Config\Database::getInstance();
        $recipientEmail = '';
        $recipientName = 'User';

        if ($source === 'manager') {
            $fetch = $db->prepare("SELECT user_id, manager_name, email, company_email, status FROM managers WHERE id = :id LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: [];
            $recipientEmail = (string)($row['email'] ?? '');
            $companyEmail = (string)($row['company_email'] ?? '');
            $recipientName = (string)($row['manager_name'] ?? 'Manager');
            $linkedUserId = (int)($row['user_id'] ?? 0);

            $stmt = $db->prepare("UPDATE managers SET status = 'active', updated_at = :updated_at WHERE id = :id AND status = 'pending'");
            $stmt->execute([
                ':id' => $id,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($linkedUserId > 0 || $recipientEmail !== '' || $companyEmail !== '') {
                $this->syncLinkedUserStatus($linkedUserId, $recipientEmail, $companyEmail, 'active');
            }
        } elseif ($source === 'user') {
            $fetch = $db->prepare("SELECT name, email, company_email, status FROM users WHERE id = :id AND role = 'asm_manager' LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: [];
            $recipientEmail = (string)($row['email'] ?? '');
            $companyEmail = (string)($row['company_email'] ?? '');
            $recipientName = (string)($row['name'] ?? 'User');

            $stmt = $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE id = :id AND role = 'asm_manager' AND status = 'pending'");
            $stmt->execute([
                ':id' => $id,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);

            $managerUpdate = $db->prepare("
                UPDATE managers
                SET status = 'active', updated_at = :updated_at
                WHERE user_id = :user_id
                   OR (:email_filter <> '' AND email = :email_value)
                   OR (:company_email_filter <> '' AND company_email = :company_email_value)
            ");
            $managerUpdate->execute([
                ':updated_at' => date('Y-m-d H:i:s'),
                ':user_id' => $id,
                ':email_filter' => $recipientEmail,
                ':email_value' => $recipientEmail,
                ':company_email_filter' => $companyEmail,
                ':company_email_value' => $companyEmail,
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

    public function delete(string $source, int $id): void
    {
        Csrf::verify();

        $db = Database::getInstance();
        $deleted = false;

        if ($source === 'manager') {
            $fetch = $db->prepare("SELECT id, user_id, email, company_email FROM managers WHERE id = :id LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                Session::flash('message', 'Manager record not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('managers'));
            }

            $del = $db->prepare("DELETE FROM managers WHERE id = :id LIMIT 1");
            $del->execute([':id' => $id]);
            $deleted = $del->rowCount() > 0;

            $linkedUserId = (int)($row['user_id'] ?? 0);
            if ($linkedUserId > 0) {
                $db->prepare("DELETE FROM users WHERE id = :id AND role = 'asm_manager' LIMIT 1")
                    ->execute([':id' => $linkedUserId]);
            }
        } elseif ($source === 'user') {
            $fetch = $db->prepare("SELECT id, email, company_email FROM users WHERE id = :id AND role = 'asm_manager' LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                Session::flash('message', 'Manager user not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('managers'));
            }

            $delUser = $db->prepare("DELETE FROM users WHERE id = :id AND role = 'asm_manager' LIMIT 1");
            $delUser->execute([':id' => $id]);
            $deleted = $delUser->rowCount() > 0;

            $email = (string)($row['email'] ?? '');
            $companyEmail = (string)($row['company_email'] ?? '');
            if ($email !== '' || $companyEmail !== '') {
                $db->prepare("DELETE FROM managers WHERE email = :email OR company_email = :company_email")
                    ->execute([':email' => $email, ':company_email' => $companyEmail]);
            }
        } else {
            Session::flash('message', 'Invalid manager source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('managers'));
        }

        if ($deleted) {
            (new ActivityLogModel())->log(Auth::id(), 'manager_delete', "Deleted manager {$source} ID: {$id}");
            Session::flash('message', 'Manager deleted successfully.');
            Session::flash('message_type', 'success');
        } else {
            Session::flash('message', 'No manager record was deleted.');
            Session::flash('message_type', 'info');
        }

        $this->redirect(App::url('managers'));
    }
}
