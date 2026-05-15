<?php

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Config\App;
use App\Config\Router;

$router = new Router();

require_once App::basePath('routes/web.php');
require_once App::basePath('routes/api.php');

try {
    $response = $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

    if (is_string($response)) {
        echo $response;
    }
} catch (Throwable $e) {
    error_log('Unhandled exception: ' . $e->getMessage());
    http_response_code(500);
    if (App::isApiRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Server Error', 'code' => 500]);
        exit;
    }
    include_once App::basePath('app/Views/auth/errors/500.php');
}
