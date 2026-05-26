<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;

class AddressProvinceController extends BaseController
{
    public function __construct()
    {
        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $rows = [
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
            ['region' => 'VISMIN', 'province' => 'SURIGAO DEL SUR'],
        ];

        return $this->render('super_admin.address.province', [
            'title' => 'Province',
            'rows' => $rows,
        ]);
    }
}
