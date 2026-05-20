<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\InstallerTechDataModel;
use App\Models\InstallerTechTeamAreaModel;

class InstallersController extends BaseController
{
    private InstallerTechDataModel $techDataModel;
    private InstallerTechTeamAreaModel $techTeamAreaModel;

    public function __construct()
    {
        $this->techDataModel = new InstallerTechDataModel();
        $this->techTeamAreaModel = new InstallerTechTeamAreaModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function create(): string
    {
        return $this->render('installers.create', [
            'title' => 'Add Installer',
        ]);
    }

    public function techData(): string
    {
        $rows = $this->techDataModel->all(['id', 'installer_name', 'full_name', 'type', 'category', 'area', 'validation_status', 'check_surf2sawa', 'check_fiberx', 'check_bida', 'check_sme', 'status', 'created_at'], [], ['id' => 'DESC']);

        return $this->render('installers.tech-data', [
            'title' => 'Tech Data',
            'rows' => $rows,
        ]);
    }

    public function storeTechData(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $installerName = trim($data['installer_name'] ?? '');
        $fullName = trim($data['full_name'] ?? '');
        $type = trim($data['type'] ?? '');
        $category = trim($data['category'] ?? '');
        $area = trim($data['area'] ?? '');
        $validationStatus = trim($data['validation_status'] ?? 'pending');
        $checkSurf2Sawa = isset($data['check_surf2sawa']) ? 1 : 0;
        $checkFiberx = isset($data['check_fiberx']) ? 1 : 0;
        $checkBida = isset($data['check_bida']) ? 1 : 0;
        $checkSme = isset($data['check_sme']) ? 1 : 0;
        $status = trim($data['status'] ?? 'active');

        $validator = (new Validation())->validate(
            [
                'installer_name' => $installerName,
                'full_name' => $fullName,
                'type' => $type,
                'category' => $category,
                'area' => $area,
                'validation_status' => $validationStatus,
                'status' => $status,
            ],
            [
                'installer_name' => 'required|min:2|max:150',
                'full_name' => 'required|min:2|max:150',
                'type' => 'required|min:2|max:50',
                'category' => 'required|min:2|max:100',
                'area' => 'required|min:2|max:150',
                'validation_status' => 'required|in:approved,pending,declined',
                'status' => 'required|in:active,inactive',
            ]
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add tech data. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('installers/tech-data'));
        }

        $this->techDataModel->createTechData([
            'installer_name' => $installerName,
            'full_name' => $fullName,
            'type' => $type,
            'category' => $category,
            'area' => $area,
            'validation_status' => $validationStatus,
            'check_surf2sawa' => $checkSurf2Sawa,
            'check_fiberx' => $checkFiberx,
            'check_bida' => $checkBida,
            'check_sme' => $checkSme,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'installer_tech_data_create', "Created installer tech data: {$installerName}");

        Session::flash('message', 'Tech data added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('installers/tech-data'));
    }

    public function techTeamArea(): string
    {
        $rows = $this->techTeamAreaModel->all(['id', 'area', 'team', 'validation_status', 'status', 'created_at'], [], ['id' => 'DESC']);

        return $this->render('installers.tech-team-area', [
            'title' => 'Tech Team Area',
            'rows' => $rows,
        ]);
    }

    public function storeTechTeamArea(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $area = trim($data['area'] ?? '');
        $team = trim($data['team'] ?? '');
        $validationStatus = trim($data['validation_status'] ?? 'pending');
        $status = trim($data['status'] ?? 'active');

        $validator = (new Validation())->validate(
            [
                'area' => $area,
                'team' => $team,
                'validation_status' => $validationStatus,
                'status' => $status,
            ],
            [
                'area' => 'required|min:2|max:150',
                'team' => 'required|min:1|max:100',
                'validation_status' => 'required|in:approved,pending,declined',
                'status' => 'required|in:active,inactive',
            ]
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add tech team area. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('installers/tech-team-area'));
        }

        $this->techTeamAreaModel->createTeamArea([
            'area' => $area,
            'team' => $team,
            'validation_status' => $validationStatus,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'installer_tech_team_area_create', "Created tech team area: {$area} - {$team}");

        Session::flash('message', 'Tech team area added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('installers/tech-team-area'));
    }
}
