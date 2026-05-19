<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\AdminModel;

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
            ['id', 'first_name', 'last_name', 'position', 'area', 'contact_no', 'employee_id', 'department', 'company_email', 'email', 'profile_picture', 'status', 'created_at'],
            [],
            ['id' => 'DESC']
        );

        return $this->render('admins.index', [
            'title' => 'Admin',
            'admins' => $admins,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $first_name = trim($data['first_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $position = trim($data['position'] ?? '');
        $area = trim($data['area'] ?? '');
        $contact_no = trim($data['contact_no'] ?? '');
        $employee_id = trim($data['employee_id'] ?? '');
        $department = trim($data['department'] ?? '');
        $company_email = trim($data['company_email'] ?? '');
        $email = trim($data['email'] ?? '');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'position' => 'required|max:100',
            'contact_no' => 'required|max:30',
            'employee_id' => 'required|max:50|unique:admins.employee_id',
            'department' => 'required|max:100',
            'company_email' => 'required|email|unique:admins.company_email',
            'email' => 'required|email|unique:admins.email',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('first_name', 'last_name', 'position', 'contact_no', 'employee_id', 'department', 'company_email', 'email', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add admin. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $profilePicturePath = null;
        if (isset($_FILES['profile_picture']) && ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $profilePicturePath = Upload::store($_FILES['profile_picture'], 'admins', 'adm_');
            if ($profilePicturePath === null) {
                Session::flash('message', 'Profile picture upload failed. Please use a valid image file (max 5MB).');
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $this->adminModel->createAdmin([
            'user_id' => null,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'position' => $position,
            'area' => $area ?: null,
            'contact_no' => $contact_no ?: null,
            'employee_id' => $employee_id,
            'department' => $department,
            'company_email' => $company_email,
            'email' => $email,
            'profile_picture' => $profilePicturePath,
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
            'last_name' => trim($data['last_name'] ?? ''),
            'position' => trim($data['position'] ?? ''),
            'area' => trim($data['area'] ?? ''),
            'contact_no' => trim($data['contact_no'] ?? ''),
            'employee_id' => trim($data['employee_id'] ?? ''),
            'department' => trim($data['department'] ?? ''),
            'company_email' => trim($data['company_email'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'status' => trim($data['status'] ?? 'active'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $stmt = $db->prepare("
            UPDATE admins
            SET first_name = :first_name,
                last_name = :last_name,
                position = :position,
                area = :area,
                contact_no = :contact_no,
                employee_id = :employee_id,
                department = :department,
                company_email = :company_email,
                email = :email,
                status = :status,
                updated_at = :updated_at
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([
            ':id' => $id,
            ':first_name' => $payload['first_name'],
            ':last_name' => $payload['last_name'],
            ':position' => $payload['position'],
            ':area' => $payload['area'] !== '' ? $payload['area'] : null,
            ':contact_no' => $payload['contact_no'] !== '' ? $payload['contact_no'] : null,
            ':employee_id' => $payload['employee_id'],
            ':department' => $payload['department'],
            ':company_email' => $payload['company_email'],
            ':email' => $payload['email'],
            ':status' => $payload['status'],
            ':updated_at' => $payload['updated_at'],
        ]);

        $linkedUserId = (int)($existing['user_id'] ?? 0);
        if ($linkedUserId > 0) {
            $fullName = trim($payload['first_name'] . ' ' . $payload['last_name']);
            $userStmt = $db->prepare("
                UPDATE users
                SET name = :name,
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
                ':email' => $payload['email'],
                ':company_email' => $payload['company_email'] !== '' ? $payload['company_email'] : null,
                ':status' => $payload['status'],
                ':updated_at' => $payload['updated_at'],
            ]);
        }

        Session::flash('message', 'Admin updated successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('admins'));
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
