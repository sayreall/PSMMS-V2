<?php

namespace App\Controllers\Users\SuperAdmin;

use App\Config\App;
use App\Controllers\BaseController;
use App\Helpers\Auth;

class AddressRegionController extends BaseController
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
            ['name' => 'VISMIN'],
            ['name' => 'SLB'],
            ['name' => 'NCRR'],
            ['name' => 'NCL2'],
        ];

        return $this->render('super_admin.address.region', [
            'title' => 'Region',
            'rows' => $rows,
        ]);
    }
}
