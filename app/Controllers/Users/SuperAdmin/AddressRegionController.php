<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Validation;
use App\Models\Users\SuperAdmin\AddressRegionModel;

class AddressRegionController extends BaseController
{
    private AddressRegionModel $regionModel;

    public function __construct()
    {
        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }

        $this->regionModel = new AddressRegionModel();
    }

    public function index(): string
    {
        $rows = array_map(
            static fn(array $row): array => [
                'id' => (int)($row['id'] ?? 0),
                'name' => (string)($row['region_name'] ?? ''),
                'code' => (string)($row['region_code'] ?? ''),
            ],
            $this->regionModel->getForTable()
        );

        return $this->render('super_admin.address.region', [
            'title' => 'Region',
            'rows' => $rows,
            'philippineRegions' => $this->philippineRegions(),
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $regionName = strtoupper(trim((string)($data['region_name'] ?? '')));
        $regionCode = strtoupper(trim((string)($data['region_code'] ?? '')));

        $validator = (new Validation())->validate(
            ['region_name' => $regionName],
            ['region_name' => 'required|min:2|max:120']
        );

        if (!$validator->passes()) {
            $this->flash($validator->first('region_name') ?? 'Invalid region name.', 'error');
            $this->redirect(App::url('address/region'));
        }

        if ($this->regionModel->findByName($regionName)) {
            $this->flash('Region already exists.', 'error');
            $this->redirect(App::url('address/region'));
        }

        $this->regionModel->create([
            'region_name' => $regionName,
            'region_code' => $regionCode !== '' ? $regionCode : null,
            'status' => 'active',
        ]);

        $this->flash('Region added successfully.');
        $this->redirect(App::url('address/region'));
    }

    public function update(int $id): void
    {
        Csrf::verify();

        $region = $this->regionModel->find($id, ['id']);
        if (!$region) {
            $this->flash('Region not found.', 'error');
            $this->redirect(App::url('address/region'));
        }

        $data = $this->requestData();
        $regionName = strtoupper(trim((string)($data['region_name'] ?? '')));
        $regionCode = strtoupper(trim((string)($data['region_code'] ?? '')));

        $validator = (new Validation())->validate(
            ['region_name' => $regionName],
            ['region_name' => 'required|min:2|max:120']
        );

        if (!$validator->passes()) {
            $this->flash($validator->first('region_name') ?? 'Invalid region name.', 'error');
            $this->redirect(App::url('address/region'));
        }

        $duplicate = $this->regionModel->findByName($regionName);
        if ($duplicate && (int)$duplicate['id'] !== $id) {
            $this->flash('Region already exists.', 'error');
            $this->redirect(App::url('address/region'));
        }

        try {
            $this->regionModel->update($id, [
                'region_name' => $regionName,
                'region_code' => $regionCode !== '' ? $regionCode : null,
            ]);
            $this->flash('Region updated successfully.');
        } catch (\Throwable $e) {
            $this->flash('Unable to update region. Please check for duplicate code values.', 'error');
        }

        $this->redirect(App::url('address/region'));
    }

    public function delete(int $id): void
    {
        Csrf::verify();

        try {
            if ($this->regionModel->delete($id)) {
                $this->flash('Region deleted successfully.');
            } else {
                $this->flash('Region not found.', 'error');
            }
        } catch (\Throwable $e) {
            $this->flash('Unable to delete region because it still has provinces.', 'error');
        }

        $this->redirect(App::url('address/region'));
    }

    private function philippineRegions(): array
    {
        return [
            ['code' => 'NCLZ', 'name' => 'NCLZ'],
            ['code' => 'NCR', 'name' => 'NCR'],
            ['code' => 'SLB', 'name' => 'SLB'],
            ['code' => 'VIZMIN', 'name' => 'VIZMIN'],
        ];
    }
}
