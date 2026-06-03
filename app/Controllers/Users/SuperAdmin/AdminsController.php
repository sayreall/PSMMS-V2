<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\Users\SuperAdmin\AdminModel;

class AdminsController extends BaseController
{
    private AdminModel $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $admins = $this->adminModel->all(
            ['id', 'admin_id', 'user_type', 'first_name', 'middle_name', 'last_name', 'username', 'position', 'area', 'contact_no', 'contact', 'address', 'employee_id', 'department', 'company_email', 'email', 'photos', 'status', 'created_at'],
            [],
            ['id' => 'DESC']
        );

        return $this->render('super_admin.admins.index', [
            'title' => 'Admin',
            'admins' => $admins,
        ]);
    }


    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $username = trim($data['username'] ?? '');
        $position = trim($data['position'] ?? '');
        $area = trim($data['area'] ?? '');
        $contact_no = trim($data['contact_no'] ?? '');
        $address = trim($data['address'] ?? '');
        $employee_id = trim($data['employee_id'] ?? '');
        $department = trim($data['department'] ?? '');
        $company_email = trim($data['company_email'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim((string)($data['password'] ?? ''));
        $status = trim($data['status'] ?? 'active');
        $username = $username !== '' ? $username : $this->composeFullName($first_name, $middle_name, $last_name);

        $rules = [
            'first_name' => 'required|min:2|max:100',
            'middle_name' => 'max:100',
            'last_name' => 'required|min:2|max:100',
            'username' => 'max:100',
            'position' => 'required|max:100',
            'contact_no' => 'required|max:30',
            'address' => 'max:255',
            'employee_id' => 'required|max:50|unique:admins.employee_id',
            'department' => 'required|in:operation,accounting',
            'company_email' => 'required|email|unique:admins.company_email',
            'email' => 'required|email|unique:admins.email',
            'password' => 'min:8',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('first_name', 'middle_name', 'last_name', 'username', 'position', 'contact_no', 'address', 'employee_id', 'department', 'company_email', 'email', 'password', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add admin. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $photoPath = null;
        if (isset($_FILES['photos']) && ($_FILES['photos']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photoPath = Upload::store($_FILES['photos'], 'admins', 'adm_');
            if ($photoPath === null) {
                Session::flash('message', 'Profile picture upload failed. Please use a valid image file (max 5MB).');
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $this->adminModel->createAdmin([
            'user_id' => null,
            'user_type' => 'admin',
            'first_name' => $first_name,
            'middle_name' => $middle_name ?: null,
            'last_name' => $last_name,
            'username' => $username,
            'position' => $position,
            'area' => $area ?: null,
            'contact_no' => $contact_no ?: null,
            'contact' => $contact_no ?: null,
            'address' => $address ?: null,
            'employee_id' => $employee_id,
            'department' => $department,
            'company_email' => $company_email,
            'password' => $password !== '' ? password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) : null,
            'email' => $email,
            'photos' => $photoPath,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'admin_create', "Created admin: {$first_name} {$last_name}");

        Session::flash('message', 'Admin added successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('admins')]);
        }

        $this->redirect(App::url('admins'));
    }

    public function approve(int $id): void
    {
        Csrf::verify();

        $db = \App\Config\Database::getInstance();

        $fetch = $db->prepare("SELECT user_id, first_name, last_name, status FROM admins WHERE id = :id LIMIT 1");
        $fetch->execute([':id' => $id]);
        $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: [];

        if (($row['status'] ?? '') !== 'pending') {
            Session::flash('message', 'Admin is already approved or inactive.');
            Session::flash('message_type', 'info');
            $this->redirectBack();
        }

        $stmt = $db->prepare("UPDATE admins SET status = 'active', updated_at = :updated_at WHERE id = :id AND status = 'pending'");
        $stmt->execute([
            ':id' => $id,
            ':updated_at' => date('Y-m-d H:i:s'),
        ]);

        $userId = (int)($row['user_id'] ?? 0);
        if ($userId > 0) {
            $userStmt = $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE id = :id AND status = 'pending'");
            $userStmt->execute([
                ':id' => $userId,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $fullName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        (new ActivityLogModel())->log(Auth::id(), 'admin_approve', "Approved admin: {$fullName}");

        Session::flash('message', 'Admin approved successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('admins')]);
        }

        $this->redirect(App::url('admins'));
    }

    public function update(int $id): void
    {
        Csrf::verify();

        $db = \App\Config\Database::getInstance();
        $fetch = $db->prepare("SELECT id, user_id FROM admins WHERE id = :id LIMIT 1");
        $fetch->execute([':id' => $id]);
        $existing = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

        if (!$existing) {
            Session::flash('message', 'Admin not found.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $data = $this->requestData();
        $payload = [
            'first_name' => trim($data['first_name'] ?? ''),
            'middle_name' => trim($data['middle_name'] ?? ''),
            'last_name' => trim($data['last_name'] ?? ''),
            'username' => trim($data['username'] ?? ''),
            'position' => trim($data['position'] ?? ''),
            'area' => trim($data['area'] ?? ''),
            'contact_no' => trim($data['contact_no'] ?? ''),
            'address' => trim($data['address'] ?? ''),
            'employee_id' => trim($data['employee_id'] ?? ''),
            'department' => trim($data['department'] ?? ''),
            'company_email' => trim($data['company_email'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'status' => trim($data['status'] ?? 'active'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!in_array($payload['department'], ['operation', 'accounting'], true)) {
            Session::flash('message', 'Department must be Operation or Accounting.');
            Session::flash('message_type', 'error');

            if (App::isAjax() || App::isApiRequest()) {
                $this->json(['success' => false, 'message' => 'Department must be Operation or Accounting.'], 422);
            }

            $this->redirectBack();
        }

        $stmt = $db->prepare("
            UPDATE admins
            SET first_name = :first_name,
                middle_name = :middle_name,
                username = :username,
                last_name = :last_name,
                position = :position,
                area = :area,
                contact_no = :contact_no,
                contact = :contact,
                address = :address,
                employee_id = :employee_id,
                department = :department,
                company_email = :company_email,
                email = :email,
                user_type = COALESCE(user_type, 'admin'),
                status = :status,
                updated_at = :updated_at
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([
            ':id' => $id,
            ':first_name' => $payload['first_name'],
            ':middle_name' => $payload['middle_name'] !== '' ? $payload['middle_name'] : null,
            ':username' => $payload['username'] !== '' ? $payload['username'] : $this->composeFullName($payload['first_name'], $payload['middle_name'], $payload['last_name']),
            ':last_name' => $payload['last_name'],
            ':position' => $payload['position'],
            ':area' => $payload['area'] !== '' ? $payload['area'] : null,
            ':contact_no' => $payload['contact_no'] !== '' ? $payload['contact_no'] : null,
            ':contact' => $payload['contact_no'] !== '' ? $payload['contact_no'] : null,
            ':address' => $payload['address'] !== '' ? $payload['address'] : null,
            ':employee_id' => $payload['employee_id'],
            ':department' => $payload['department'],
            ':company_email' => $payload['company_email'],
            ':email' => $payload['email'],
            ':status' => $payload['status'],
            ':updated_at' => $payload['updated_at'],
        ]);

        $linkedUserId = (int)($existing['user_id'] ?? 0);
        if ($linkedUserId > 0) {
            $fullName = $this->composeFullName($payload['first_name'], $payload['middle_name'], $payload['last_name']);
            $userStmt = $db->prepare("
                UPDATE users
                SET name = :name,
                    middle_name = :middle_name,
                    contact_no = :contact_no,
                    email = :email,
                    company_email = :company_email,
                    status = :status,
                    updated_at = :updated_at
                WHERE id = :id
                LIMIT 1
            ");
            $userStmt->execute([
                ':id' => $linkedUserId,
                ':name' => $fullName,
                ':middle_name' => $payload['middle_name'] !== '' ? $payload['middle_name'] : null,
                ':contact_no' => $payload['contact_no'] !== '' ? $payload['contact_no'] : null,
                ':email' => $payload['email'],
                ':company_email' => $payload['company_email'] !== '' ? $payload['company_email'] : null,
                ':status' => $payload['status'],
                ':updated_at' => $payload['updated_at'],
            ]);
        }

        Session::flash('message', 'Admin updated successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('admins')]);
        }

        $this->redirect(App::url('admins'));
    }

    private function composeFullName(string $first, string $middle, string $last): string
    {
        return trim(preg_replace('/\s+/', ' ', trim("$first $middle $last")) ?? '');
    }

    public function delete(int $id): void
    {
        Csrf::verify();

        $db = \App\Config\Database::getInstance();
        $fetch = $db->prepare("SELECT user_id, first_name, last_name FROM admins WHERE id = :id LIMIT 1");
        $fetch->execute([':id' => $id]);
        $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

        if (!$row) {
            Session::flash('message', 'Admin not found.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $del = $db->prepare("DELETE FROM admins WHERE id = :id LIMIT 1");
        $del->execute([':id' => $id]);

        $userId = (int)($row['user_id'] ?? 0);
        if ($userId > 0) {
            $db->prepare("DELETE FROM users WHERE id = :id LIMIT 1")->execute([':id' => $userId]);
        }

        $fullName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        (new ActivityLogModel())->log(Auth::id(), 'admin_delete', "Deleted admin: {$fullName}");

        Session::flash('message', 'Admin deleted successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('admins'));
    }
}
