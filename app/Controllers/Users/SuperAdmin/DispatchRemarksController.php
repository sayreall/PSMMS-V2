<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\Users\SuperAdmin\DispatchRemarksModel;

class DispatchRemarksController extends BaseController
{
    private DispatchRemarksModel $dispatchRemarksModel;

    public function __construct()
    {
        $this->dispatchRemarksModel = new DispatchRemarksModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $rows = $this->dispatchRemarksModel->all(
            ['id', 'dispatch_remarks', 'created_at'],
            [],
            ['id' => 'ASC']
        );

        return $this->render('super_admin.dispatch.remarks', [
            'title' => 'Dispatch Remarks',
            'rows' => $rows,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $dispatchRemarks = trim($data['dispatch_remarks'] ?? '');

        $rules = [
            'dispatch_remarks' => 'required|min:2|max:150',
        ];

        $validator = (new Validation())->validate(
            ['dispatch_remarks' => $dispatchRemarks],
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add dispatch remarks. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        try {
            $this->dispatchRemarksModel->createDispatchRemarks([
                'dispatch_remarks' => $dispatchRemarks,
            ]);
        } catch (\Throwable $e) {
            error_log('Dispatch remarks create failed: ' . $e->getMessage());
            Session::flash('message', 'Dispatch remarks already exists or could not be saved.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        (new ActivityLogModel())->log(Auth::id(), 'dispatch_remarks_create', "Created dispatch remarks: {$dispatchRemarks}");

        Session::flash('message', 'Dispatch remarks added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('dispatch-record/dispatch-remarks'));
    }
}
