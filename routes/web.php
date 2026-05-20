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
    $router->get('/dashboard/{slug}', 'DashboardController@roleDashboard');
    $router->get('/dashboard/profile', 'DashboardController@profile');
    $router->post('/dashboard/profile', 'DashboardController@updateProfile');
    $router->post('/dashboard/password', 'DashboardController@updatePassword');
    $router->get('/dashboard/stats', 'DashboardController@getStats');
    $router->get('/managers', 'ManagersController@index');
    $router->post('/managers', 'ManagersController@store');
    $router->put('/managers/{source}/{id}', 'ManagersController@update');
    $router->delete('/managers/{source}/{id}', 'ManagersController@delete');
    $router->post('/managers/{source}/{id}/approve', 'ManagersController@approve');
    $router->get('/inhouse', 'InhouseController@index');
    $router->post('/inhouse', 'InhouseController@store');
    $router->post('/inhouse/{source}/{id}/approve', 'InhouseController@approve');
    $router->post('/inhouse/{source}/{id}/delete', 'InhouseController@delete');
    $router->get('/partners', 'PartnersController@index');
    $router->post('/partners', 'PartnersController@store');
    $router->post('/partners/{source}/{id}/approve', 'PartnersController@approve');
    $router->post('/partners/{source}/{id}/delete', 'PartnersController@delete');
    $router->get('/admins', 'AdminsController@index');
    $router->post('/admins', 'AdminsController@store');
    $router->put('/admins/{id}', 'AdminsController@update');
    $router->delete('/admins/{id}', 'AdminsController@delete');
    $router->post('/admins/{id}/approve', 'AdminsController@approve');
    $router->get('/plan', 'PlansController@index');
    $router->post('/plan', 'PlansController@store');
    $router->get('/installers/create', 'InstallersController@create');
    $router->get('/installers/tech-data', 'InstallersController@techData');
    $router->post('/installers/tech-data', 'InstallersController@storeTechData');
    $router->get('/installers/tech-team-area', 'InstallersController@techTeamArea');
    $router->get('/sales-category', 'SalesCategoryController@index');
    $router->post('/sales-category', 'SalesCategoryController@store');
    $router->get('/sales-agent', 'SalesAgentController@index');
    $router->post('/sales-agent', 'SalesAgentController@store');
    $router->get('/agent-code', 'AgentCodeController@index');
    $router->post('/agent-code', 'AgentCodeController@store');
    $router->get('/address/region', 'AddressRegionController@index');
    $router->get('/address/province', 'AddressProvinceController@index');
}, [AuthMiddleware::class]);

$router->group('/users', function (Router $router): void {
    $router->get('', 'UsersController@index');
    $router->get('/create', 'UsersController@create');
    $router->post('', 'UsersController@store');
    $router->post('/{id}/approve', 'UsersController@approve');
    $router->get('/{id}/edit', 'UsersController@edit');
    $router->put('/{id}', 'UsersController@update');
    $router->delete('/{id}', 'UsersController@delete');
}, [AdminMiddleware::class]);
