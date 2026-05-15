<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\ManagerModel;

class ManagersController extends BaseController
{
    private ManagerModel $managerModel;

    public function __construct()
    {
        $this->managerModel = new ManagerModel();
    }

    public function index(): string
    {
        $managers = $this->managerModel->all(
            ['id', 'manager_name', 'position', 'contact_no', 'company_email', 'email', 'profile_picture', 'status', 'created_at'],
            [],
            ['id' => 'DESC']
        );

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

        $this->managerModel->createManager([
            'user_id' => null,
            'manager_name' => $manager_name,
            'position' => $position,
            'contact_no' => $contact_no ?: null,
            'company_email' => $company_email,
            'email' => $email,
            'profile_picture' => $profilePicturePath,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'manager_create', "Created manager: {$manager_name}");

        Session::flash('message', 'Manager added successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('managers')]);
        }

        $this->redirect(App::url('managers'));
    }
}
