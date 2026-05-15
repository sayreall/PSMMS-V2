<?php

use App\Config\Router;
use App\Middleware\ApiAuthMiddleware;

/** @var Router $router */

$router->group('/api', function (Router $router): void {
    $router->post('/auth/login', 'Api\\AuthController@login');

    $router->get('/stats', 'Api\\DashboardController@stats', [ApiAuthMiddleware::class]);

    $router->get('/users', 'Api\\UsersController@index', [ApiAuthMiddleware::class]);
    $router->post('/users', 'Api\\UsersController@store', [ApiAuthMiddleware::class]);
    $router->put('/users/{id}', 'Api\\UsersController@update', [ApiAuthMiddleware::class]);
    $router->delete('/users/{id}', 'Api\\UsersController@delete', [ApiAuthMiddleware::class]);
});
