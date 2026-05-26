<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Models\Users\SuperAdmin\AddressProvinceModel;

class AddressProvinceController extends BaseController
{
    private AddressProvinceModel $provinceModel;

    public function __construct()
    {
        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }

        $this->provinceModel = new AddressProvinceModel();
    }

    public function index(): string
    {
        $rows = $this->provinceModel->getForTable();

        return $this->render('super_admin.address.province', [
            'title' => 'Province',
            'rows' => $rows,
        ]);
    }
}
