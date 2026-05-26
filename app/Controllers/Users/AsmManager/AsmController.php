<?php

namespace App\Controllers\Users\AsmManager;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\Users\AsmManager\AsmNameModel;
use App\Models\Users\AsmManager\AsmPerAreaModel;

class AsmController extends BaseController
{
    private AsmNameModel $asmNameModel;
    private AsmPerAreaModel $asmPerAreaModel;

    public function __construct()
    {
        $this->asmNameModel = new AsmNameModel();
        $this->asmPerAreaModel = new AsmPerAreaModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function name(): string
    {
        $rows = $this->asmNameModel->all(['id', 'name', 'validation_status', 'check_surf2sawa', 'check_fiberx', 'check_bida', 'check_sme', 'status', 'created_at'], [], ['id' => 'DESC']);

        return $this->render('asm_manager.asm.name', [
            'title' => 'ASM Names',
            'rows' => $rows,
        ]);
    }

    public function perArea(): string
    {
        $rows = $this->asmPerAreaModel->all(['id', 'sales_manager', 'region', 'province', 'municipality', 'validation_status', 'check_surf2sawa', 'check_fiberx', 'check_bida', 'check_sme', 'status', 'created_at'], [], ['id' => 'DESC']);

        return $this->render('asm_manager.asm.per-area', [
            'title' => 'ASM Per Area',
            'rows' => $rows,
        ]);
    }

    public function storeName(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $name = trim($data['name'] ?? '');
        $validationStatus = trim($data['validation_status'] ?? 'pending');
        $checkSurf2Sawa = isset($data['check_surf2sawa']) ? 1 : 0;
        $checkFiberx = isset($data['check_fiberx']) ? 1 : 0;
        $checkBida = isset($data['check_bida']) ? 1 : 0;
        $checkSme = isset($data['check_sme']) ? 1 : 0;
        $status = trim($data['status'] ?? 'active');

        $validator = (new Validation())->validate(
            [
                'name' => $name,
                'validation_status' => $validationStatus,
                'status' => $status,
            ],
            [
                'name' => 'required|min:2|max:150',
                'validation_status' => 'required|in:approved,pending,declined',
                'status' => 'required|in:active,inactive',
            ]
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add ASM name. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('asm/name'));
        }

        $this->asmNameModel->createAsmName([
            'name' => $name,
            'validation_status' => $validationStatus,
            'check_surf2sawa' => $checkSurf2Sawa,
            'check_fiberx' => $checkFiberx,
            'check_bida' => $checkBida,
            'check_sme' => $checkSme,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'asm_name_create', "Created ASM Name: {$name}");
        Session::flash('message', 'ASM name added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('asm/name'));
    }

    public function storePerArea(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $salesManager = trim($data['sales_manager'] ?? '');
        $region = trim($data['region'] ?? '');
        $province = trim($data['province'] ?? '');
        $municipality = trim($data['municipality'] ?? '');
        $validationStatus = trim($data['validation_status'] ?? 'pending');
        $checkSurf2Sawa = isset($data['check_surf2sawa']) ? 1 : 0;
        $checkFiberx = isset($data['check_fiberx']) ? 1 : 0;
        $checkBida = isset($data['check_bida']) ? 1 : 0;
        $checkSme = isset($data['check_sme']) ? 1 : 0;
        $status = trim($data['status'] ?? 'active');

        $validator = (new Validation())->validate(
            [
                'sales_manager' => $salesManager,
                'region' => $region,
                'province' => $province,
                'municipality' => $municipality,
                'validation_status' => $validationStatus,
                'status' => $status,
            ],
            [
                'sales_manager' => 'required|min:2|max:150',
                'region' => 'required|min:2|max:80',
                'province' => 'required|min:2|max:120',
                'municipality' => 'required|min:2|max:120',
                'validation_status' => 'required|in:approved,pending,declined',
                'status' => 'required|in:active,inactive',
            ]
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add ASM per area. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('asm/per-area'));
        }

        $this->asmPerAreaModel->createAsmPerArea([
            'sales_manager' => $salesManager,
            'region' => $region,
            'province' => $province,
            'municipality' => $municipality,
            'validation_status' => $validationStatus,
            'check_surf2sawa' => $checkSurf2Sawa,
            'check_fiberx' => $checkFiberx,
            'check_bida' => $checkBida,
            'check_sme' => $checkSme,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'asm_per_area_create', "Created ASM Per Area: {$salesManager} - {$municipality}");
        Session::flash('message', 'ASM per area added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('asm/per-area'));
    }
}
