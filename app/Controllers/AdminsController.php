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
}
