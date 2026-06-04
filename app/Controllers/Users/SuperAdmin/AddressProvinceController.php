<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Validation;
use App\Models\Users\SuperAdmin\AddressProvinceModel;
use App\Models\Users\SuperAdmin\AddressRegionModel;

class AddressProvinceController extends BaseController
{
    private AddressProvinceModel $provinceModel;
    private AddressRegionModel $regionModel;

    public function __construct()
    {
        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }

        $this->provinceModel = new AddressProvinceModel();
        $this->regionModel = new AddressRegionModel();
    }

    public function index(): string
    {
        $selectedRegionId = max(0, (int)($_GET['region_id'] ?? 0));
        $rows = $this->provinceModel->getForTable($selectedRegionId > 0 ? $selectedRegionId : null);

        return $this->render('super_admin.address.province', [
            'title' => 'Province',
            'rows' => $rows,
            'regions' => $this->regionModel->getForTable(),
            'selectedRegionId' => $selectedRegionId,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $regionId = (int)($data['region_id'] ?? 0);
        $provinceName = strtoupper(trim((string)($data['province_name'] ?? '')));
        $provinceCode = trim((string)($data['province_code'] ?? ''));

        $validator = (new Validation())->validate(
            [
                'region_id' => $regionId,
                'province_name' => $provinceName,
            ],
            [
                'region_id' => 'required',
                'province_name' => 'required|min:2|max:120',
            ]
        );

        if (!$validator->passes() || !$this->regionModel->find($regionId, ['id'])) {
            $this->flash('Please select a valid region and province name.', 'error');
            $this->redirect(App::url('address/province'));
        }

        if ($this->provinceModel->findByRegionAndName($regionId, $provinceName)) {
            $this->flash('Province already exists for this region.', 'error');
            $this->redirect(App::url('address/province?region_id=' . $regionId));
        }

        $this->provinceModel->create([
            'region_id' => $regionId,
            'province_name' => $provinceName,
            'province_code' => $provinceCode !== '' ? $provinceCode : null,
            'status' => 'active',
        ]);

        $this->flash('Province added successfully.');
        $this->redirect(App::url('address/province?region_id=' . $regionId));
    }

    public function update(int $id): void
    {
        Csrf::verify();

        $province = $this->provinceModel->find($id, ['id']);
        if (!$province) {
            $this->flash('Province not found.', 'error');
            $this->redirect(App::url('address/province'));
        }

        $data = $this->requestData();
        $regionId = (int)($data['region_id'] ?? 0);
        $provinceName = strtoupper(trim((string)($data['province_name'] ?? '')));
        $provinceCode = trim((string)($data['province_code'] ?? ''));

        $validator = (new Validation())->validate(
            [
                'region_id' => $regionId,
                'province_name' => $provinceName,
            ],
            [
                'region_id' => 'required',
                'province_name' => 'required|min:2|max:120',
            ]
        );

        if (!$validator->passes() || !$this->regionModel->find($regionId, ['id'])) {
            $this->flash('Please select a valid region and province name.', 'error');
            $this->redirect(App::url('address/province'));
        }

        $duplicate = $this->provinceModel->findByRegionAndName($regionId, $provinceName);
        if ($duplicate && (int)$duplicate['id'] !== $id) {
            $this->flash('Province already exists for this region.', 'error');
            $this->redirect(App::url('address/province?region_id=' . $regionId));
        }

        try {
            $this->provinceModel->update($id, [
                'region_id' => $regionId,
                'province_name' => $provinceName,
                'province_code' => $provinceCode !== '' ? $provinceCode : null,
            ]);
            $this->flash('Province updated successfully.');
        } catch (\Throwable $e) {
            $this->flash('Unable to update province. Please check for duplicate code values.', 'error');
        }

        $this->redirect(App::url('address/province?region_id=' . $regionId));
    }

    public function delete(int $id): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $regionId = (int)($data['region_id'] ?? 0);

        try {
            if ($this->provinceModel->delete($id)) {
                $this->flash('Province deleted successfully.');
            } else {
                $this->flash('Province not found.', 'error');
            }
        } catch (\Throwable $e) {
            $this->flash('Unable to delete province because it still has municipalities.', 'error');
        }

        $this->redirect(App::url('address/province' . ($regionId > 0 ? '?region_id=' . $regionId : '')));
    }
}
