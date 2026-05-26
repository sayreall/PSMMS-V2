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
            ],
            $this->regionModel->getForTable()
        );

        return $this->render('super_admin.address.region', [
            'title' => 'Region',
            'rows' => $rows,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $regionName = strtoupper(trim((string)($data['region_name'] ?? '')));

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
            'status' => 'active',
        ]);

        $this->flash('Region added successfully.');
        $this->redirect(App::url('address/region'));
    }
}
