<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;

class AddressMunicipalityController extends BaseController
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
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
            ['region' => 'VISMIN', 'province' => 'AGUSAN DEL NORTE', 'municipality' => 'VINAPOR'],
        ];

        return $this->render('address.municipality', [
            'title' => 'Municipality',
            'rows' => $rows,
        ]);
    }
}
