<?php

namespace App\Controllers;

use App\Config\App;
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

        return $this->render('address.region', [
            'title' => 'Region',
            'rows' => $rows,
        ]);
    }
}
