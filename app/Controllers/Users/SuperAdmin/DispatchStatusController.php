<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\Users\SuperAdmin\DispatchStatusModel;

class DispatchStatusController extends BaseController
{
    private DispatchStatusModel $dispatchStatusModel;

    public function __construct()
    {
        $this->dispatchStatusModel = new DispatchStatusModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        try {
            $rows = $this->dispatchStatusModel->all(
                ['id', 'dispatch_status', 'color', 'created_at'],
                [],
                ['id' => 'ASC']
            );
        } catch (\Throwable $e) {
            error_log('Dispatch status list failed: ' . $e->getMessage());
            Session::flash('message', 'Dispatch status table is not ready yet. Please run database migrations.');
            Session::flash('message_type', 'error');
            $rows = [];
        }

        return $this->render('super_admin.dispatch.status', [
            'title' => 'Dispatch Status',
            'rows' => $rows,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $dispatchStatus = trim($data['dispatch_status'] ?? '');
        $color = trim($data['color'] ?? '');

        if ($color !== '') {
            $color = '#' . ltrim($color, '#');
        }

        $rules = [
            'dispatch_status' => 'required|min:2|max:150',
            'color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ];

        $validator = (new Validation())->validate(
            [
                'dispatch_status' => $dispatchStatus,
                'color' => $color,
            ],
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add dispatch status. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        try {
            $this->dispatchStatusModel->createDispatchStatus([
                'dispatch_status' => $dispatchStatus,
                'color' => strtolower($color),
            ]);
        } catch (\Throwable $e) {
            error_log('Dispatch status create failed: ' . $e->getMessage());
            Session::flash('message', 'Dispatch status already exists or could not be saved.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        (new ActivityLogModel())->log(Auth::id(), 'dispatch_status_create', "Created dispatch status: {$dispatchStatus}");

        Session::flash('message', 'Dispatch status added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('dispatch-record/dispatch-status'));
    }
}
