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
        $selectedRegionId = max(0, (int)($_GET['region_id'] ?? 0));
        $selectedProvinceId = max(0, (int)($_GET['province_id'] ?? 0));
        $selectedProvince = $selectedProvinceId > 0
            ? $this->provinceModel->find($selectedProvinceId, ['id', 'region_id'])
            : null;

        if ($selectedProvince && $selectedRegionId > 0 && (int)$selectedProvince['region_id'] !== $selectedRegionId) {
            $selectedProvinceId = 0;
        }

        $rows = $this->municipalityModel->getForTable(
            $selectedRegionId > 0 ? $selectedRegionId : null,
            $selectedProvinceId > 0 ? $selectedProvinceId : null
        );

        return $this->render('super_admin.address.municipality', [
            'title' => 'Municipality',
            'rows' => $rows,
            'regions' => $this->regionModel->getForTable(),
            'provinces' => $this->provinceModel->getForSelect($selectedRegionId > 0 ? $selectedRegionId : null),
            'allProvinces' => $this->provinceModel->getForSelect(),
            'selectedRegionId' => $selectedRegionId,
            'selectedProvinceId' => $selectedProvinceId,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $provinceId = (int)($data['province_id'] ?? 0);
        $regionId = (int)($data['region_id'] ?? 0);
        $provinceName = strtoupper(trim((string)($data['province_name'] ?? '')));
        $provinceCode = trim((string)($data['province_code'] ?? ''));
        $municipalityName = strtoupper(trim((string)($data['municipality'] ?? '')));
        $municipalityCode = trim((string)($data['municipality_code'] ?? ''));
        $province = $provinceId > 0 ? $this->provinceModel->find($provinceId, ['id', 'region_id']) : null;

        $validator = (new Validation())->validate(
            [
                'municipality' => $municipalityName,
            ],
            [
                'municipality' => 'required|min:2|max:120',
            ]
        );

        if (!$province && $regionId > 0 && $provinceName !== '') {
            $province = $this->provinceModel->findByRegionAndName($regionId, $provinceName);

            if (!$province && $this->regionModel->find($regionId, ['id'])) {
                $provinceId = $this->provinceModel->create([
                    'region_id' => $regionId,
                    'province_name' => $provinceName,
                    'province_code' => $provinceCode !== '' ? $provinceCode : null,
                    'status' => 'active',
                ]);
                $province = ['id' => $provinceId, 'region_id' => $regionId];
            }
        }

        if (!$validator->passes() || !$province) {
            $this->flash('Please select a valid province and municipality name.', 'error');
            $this->redirect(App::url('address/municipalities'));
        }

        $provinceId = (int)$province['id'];

        if ($this->municipalityModel->findByProvinceAndName($provinceId, $municipalityName)) {
            $this->flash('Municipality already exists for this province.', 'error');
            $this->redirect(App::url('address/municipalities?region_id=' . (int)$province['region_id'] . '&province_id=' . $provinceId));
        }

        $this->municipalityModel->create([
            'province_id' => $provinceId,
            'municipality_name' => $municipalityName,
            'municipality_code' => $municipalityCode !== '' ? $municipalityCode : null,
            'status' => 'active',
        ]);

        $this->flash('Municipality added successfully.');
        $this->redirect(App::url('address/municipalities'));
    }

    public function update(int $id): void
    {
        Csrf::verify();

        $municipality = $this->municipalityModel->find($id, ['id']);
        if (!$municipality) {
            $this->flash('Municipality not found.', 'error');
            $this->redirect(App::url('address/municipalities'));
        }

        $data = $this->requestData();
        $provinceId = (int)($data['province_id'] ?? 0);
        $municipalityName = strtoupper(trim((string)($data['municipality'] ?? '')));
        $municipalityCode = trim((string)($data['municipality_code'] ?? ''));
        $province = $provinceId > 0 ? $this->provinceModel->find($provinceId, ['id', 'region_id']) : null;

        $validator = (new Validation())->validate(
            [
                'province_id' => $provinceId,
                'municipality' => $municipalityName,
            ],
            [
                'province_id' => 'required',
                'municipality' => 'required|min:2|max:120',
            ]
        );

        if (!$validator->passes() || !$province) {
            $this->flash('Please select a valid province and municipality name.', 'error');
            $this->redirect(App::url('address/municipalities'));
        }

        $duplicate = $this->municipalityModel->findByProvinceAndName($provinceId, $municipalityName);
        if ($duplicate && (int)$duplicate['id'] !== $id) {
            $this->flash('Municipality already exists for this province.', 'error');
            $this->redirect(App::url('address/municipalities?region_id=' . (int)$province['region_id'] . '&province_id=' . $provinceId));
        }

        try {
            $this->municipalityModel->update($id, [
                'province_id' => $provinceId,
                'municipality_name' => $municipalityName,
                'municipality_code' => $municipalityCode !== '' ? $municipalityCode : null,
            ]);
            $this->flash('Municipality updated successfully.');
        } catch (\Throwable $e) {
            $this->flash('Unable to update municipality. Please check for duplicate code values.', 'error');
        }

        $this->redirect(App::url('address/municipalities?region_id=' . (int)$province['region_id'] . '&province_id=' . $provinceId));
    }

    public function delete(int $id): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $regionId = (int)($data['region_id'] ?? 0);
        $provinceId = (int)($data['province_id'] ?? 0);

        try {
            if ($this->municipalityModel->delete($id)) {
                $this->flash('Municipality deleted successfully.');
            } else {
                $this->flash('Municipality not found.', 'error');
            }
        } catch (\Throwable $e) {
            $this->flash('Unable to delete municipality.', 'error');
        }

        $query = [];
        if ($regionId > 0) {
            $query[] = 'region_id=' . $regionId;
        }
        if ($provinceId > 0) {
            $query[] = 'province_id=' . $provinceId;
        }

        $this->redirect(App::url('address/municipalities' . ($query ? '?' . implode('&', $query) : '')));
    }
}
