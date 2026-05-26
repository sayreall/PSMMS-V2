<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Validation;
use App\Models\Users\SuperAdmin\AddressMunicipalityModel;
use App\Models\Users\SuperAdmin\AddressProvinceModel;
use App\Models\Users\SuperAdmin\AddressRegionModel;

class AddressMunicipalityController extends BaseController
{
    private AddressMunicipalityModel $municipalityModel;
    private AddressProvinceModel $provinceModel;
    private AddressRegionModel $regionModel;

    public function __construct()
    {
        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }

        $this->municipalityModel = new AddressMunicipalityModel();
        $this->provinceModel = new AddressProvinceModel();
        $this->regionModel = new AddressRegionModel();
    }

    public function index(): string
    {
        $rows = $this->municipalityModel->getForTable();

        return $this->render('super_admin.address.municipality', [
            'title' => 'Municipality',
            'rows' => $rows,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $regionName = strtoupper(trim((string)($data['region'] ?? '')));
        $provinceName = strtoupper(trim((string)($data['province'] ?? '')));
        $municipalityName = strtoupper(trim((string)($data['municipality'] ?? '')));

        $validator = (new Validation())->validate(
            [
                'region' => $regionName,
                'province' => $provinceName,
                'municipality' => $municipalityName,
            ],
            [
                'region' => 'required|min:2|max:120',
                'province' => 'required|min:2|max:120',
                'municipality' => 'required|min:2|max:120',
            ]
        );

        if (!$validator->passes()) {
            $this->flash('Please provide valid Region, Province, and Municipality.', 'error');
            $this->redirect(App::url('address/municipalities'));
        }

        $region = $this->regionModel->findByName($regionName);
        if (!$region) {
            $regionId = $this->regionModel->create([
                'region_name' => $regionName,
                'status' => 'active',
            ]);
            $region = ['id' => $regionId];
        }

        $province = $this->provinceModel->findByRegionAndName((int)$region['id'], $provinceName);
        if (!$province) {
            $provinceId = $this->provinceModel->create([
                'region_id' => (int)$region['id'],
                'province_name' => $provinceName,
                'status' => 'active',
            ]);
            $province = ['id' => $provinceId];
        }

        if ($this->municipalityModel->findByProvinceAndName((int)$province['id'], $municipalityName)) {
            $this->flash('Municipality already exists for this province.', 'error');
            $this->redirect(App::url('address/municipalities'));
        }

        $this->municipalityModel->create([
            'province_id' => (int)$province['id'],
            'municipality_name' => $municipalityName,
            'status' => 'active',
        ]);

        $this->flash('Municipality added successfully.');
        $this->redirect(App::url('address/municipalities'));
    }
}
