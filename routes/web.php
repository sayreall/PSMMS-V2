<?php

use App\Config\App;
use App\Config\Router;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

/** @var Router $router */

$router->get('/', function (): void {
    header('Location: ' . App::url('dashboard'));
    exit;
});

$router->group('', function (Router $router): void {
    $router->get('/login', 'AuthController@showLogin');
    $router->post('/login', 'AuthController@login');
    $router->get('/register', 'AuthController@showRegister');
    $router->post('/register', 'AuthController@register');
    $router->get('/forgot-password', 'AuthController@showForgotPassword');
    $router->post('/forgot-password', 'AuthController@forgotPassword');
    $router->get('/verify-otp', 'AuthController@showVerifyOtp');
    $router->post('/verify-otp', 'AuthController@verifyOtp');
    $router->get('/reset-password', 'AuthController@showResetPassword');
    $router->post('/reset-password', 'AuthController@resetPassword');
}, [GuestMiddleware::class]);

$router->group('', function (Router $router): void {
    $router->post('/logout', 'AuthController@logout');

    $router->get('/dashboard', 'DashboardController@index');
    $router->get('/dashboard/profile/view', 'DashboardController@viewProfile');
    $router->get('/dashboard/profile', 'DashboardController@profile');
    $router->post('/dashboard/profile', 'DashboardController@updateProfile');
    $router->post('/dashboard/password', 'DashboardController@updatePassword');
    $router->get('/dashboard/stats', 'DashboardController@getStats');
    $router->get('/dashboard/{slug}', 'DashboardController@roleDashboard');
    $router->get('/managers', 'Users\\AsmManager\\ManagersController@index');
    $router->post('/managers', 'Users\\AsmManager\\ManagersController@store');
    $router->post('/managers/{source}/{id}/update', 'Users\\AsmManager\\ManagersController@update');
    $router->put('/managers/{source}/{id}', 'Users\\AsmManager\\ManagersController@update');
    $router->delete('/managers/{source}/{id}', 'Users\\AsmManager\\ManagersController@delete');
    $router->post('/managers/{source}/{id}/approve', 'Users\\AsmManager\\ManagersController@approve');
    $router->get('/inhouse', 'Users\\InhouseSales\\InhouseController@index');
    $router->post('/inhouse', 'Users\\InhouseSales\\InhouseController@store');
    $router->post('/inhouse/{source}/{id}/update', 'Users\\InhouseSales\\InhouseController@update');
    $router->post('/inhouse/{source}/{id}/approve', 'Users\\InhouseSales\\InhouseController@approve');
    $router->post('/inhouse/{source}/{id}/delete', 'Users\\InhouseSales\\InhouseController@delete');
    $router->get('/partners', 'Users\\MsaPartners\\PartnersController@index');
    $router->post('/partners', 'Users\\MsaPartners\\PartnersController@store');
    $router->post('/partners/{source}/{id}/update', 'Users\\MsaPartners\\PartnersController@update');
    $router->post('/partners/{source}/{id}/approve', 'Users\\MsaPartners\\PartnersController@approve');
    $router->post('/partners/{source}/{id}/delete', 'Users\\MsaPartners\\PartnersController@delete');
    $router->get('/admins', 'Users\\SuperAdmin\\AdminsController@index');
    $router->post('/admins', 'Users\\SuperAdmin\\AdminsController@store');
    $router->post('/admins/{id}/update', 'Users\\SuperAdmin\\AdminsController@update');
    $router->put('/admins/{id}', 'Users\\SuperAdmin\\AdminsController@update');
    $router->delete('/admins/{id}', 'Users\\SuperAdmin\\AdminsController@delete');
    $router->post('/admins/{id}/approve', 'Users\\SuperAdmin\\AdminsController@approve');
    $router->get('/plan', 'Users\\SuperAdmin\\PlansController@index');
    $router->post('/plan', 'Users\\SuperAdmin\\PlansController@store');
    $router->get('/dispatch-record/dispatch-status', 'Users\\SuperAdmin\\DispatchStatusController@index');
    $router->post('/dispatch-record/dispatch-status', 'Users\\SuperAdmin\\DispatchStatusController@store');
    $router->get('/dispatch-record/dispatch-remarks', 'Users\\SuperAdmin\\DispatchRemarksController@index');
    $router->post('/dispatch-record/dispatch-remarks', 'Users\\SuperAdmin\\DispatchRemarksController@store');
    $router->get('/installers/tech-data', 'Users\\SuperAdmin\\InstallersController@techData');
    $router->post('/installers/tech-data', 'Users\\SuperAdmin\\InstallersController@storeTechData');
    $router->get('/installers/tech-team-area', 'Users\\SuperAdmin\\InstallersController@techTeamArea');
    $router->post('/installers/tech-team-area', 'Users\\SuperAdmin\\InstallersController@storeTechTeamArea');
    $router->get('/asm/name', 'Users\\AsmManager\\AsmController@name');
    $router->post('/asm/name', 'Users\\AsmManager\\AsmController@storeName');
    $router->get('/asm/per-area', 'Users\\AsmManager\\AsmController@perArea');
    $router->post('/asm/per-area', 'Users\\AsmManager\\AsmController@storePerArea');
    $router->get('/sales-category', 'Users\\SmeSales\\SalesCategoryController@index');
    $router->post('/sales-category', 'Users\\SmeSales\\SalesCategoryController@store');
    $router->get('/sales-agent', 'Users\\SmeSales\\SalesAgentController@index');
    $router->post('/sales-agent', 'Users\\SmeSales\\SalesAgentController@store');
    $router->get('/agent-code', 'Users\\SmeSales\\AgentCodeController@index');
    $router->post('/agent-code', 'Users\\SmeSales\\AgentCodeController@store');
    $router->get('/address/region', 'Users\\SuperAdmin\\AddressRegionController@index');
    $router->post('/address/region', 'Users\\SuperAdmin\\AddressRegionController@store');
    $router->post('/address/region/{id}/update', 'Users\\SuperAdmin\\AddressRegionController@update');
    $router->post('/address/region/{id}/delete', 'Users\\SuperAdmin\\AddressRegionController@delete');
    $router->get('/address/province', 'Users\\SuperAdmin\\AddressProvinceController@index');
    $router->post('/address/province', 'Users\\SuperAdmin\\AddressProvinceController@store');
    $router->post('/address/province/{id}/update', 'Users\\SuperAdmin\\AddressProvinceController@update');
    $router->post('/address/province/{id}/delete', 'Users\\SuperAdmin\\AddressProvinceController@delete');
    $router->get('/address/municipalities', 'Users\\SuperAdmin\\AddressMunicipalityController@index');
    $router->post('/address/municipalities', 'Users\\SuperAdmin\\AddressMunicipalityController@store');
    $router->post('/address/municipalities/{id}/update', 'Users\\SuperAdmin\\AddressMunicipalityController@update');
    $router->post('/address/municipalities/{id}/delete', 'Users\\SuperAdmin\\AddressMunicipalityController@delete');
}, [AuthMiddleware::class]);

$router->group('/users', function (Router $router): void {
    $router->get('', 'Users\\SuperAdmin\\UsersController@index');
    $router->get('/create', 'Users\\SuperAdmin\\UsersController@create');
    $router->post('', 'Users\\SuperAdmin\\UsersController@store');
    $router->post('/{id}/approve', 'Users\\SuperAdmin\\UsersController@approve');
    $router->get('/{id}/edit', 'Users\\SuperAdmin\\UsersController@edit');
    $router->put('/{id}', 'Users\\SuperAdmin\\UsersController@update');
    $router->delete('/{id}', 'Users\\SuperAdmin\\UsersController@delete');
}, [AdminMiddleware::class]);
