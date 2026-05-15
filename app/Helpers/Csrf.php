<?php

namespace App\Helpers;

use App\Config\App;

use App\Helpers\Session;

class Csrf
{
    public static function init(): void
    {
        if (Session::get('csrf_token') === null) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
    }

    public static function token(): string
    {
        return Session::csrfToken();
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::token() . '">';
    }

    public static function validate(string $token): bool
    {
        return Session::validateCsrf($token);
    }

    public static function verify(): void
    {
        if (App::isApiRequest()) {
            return;
        }

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!$token || !self::validate($token)) {
                http_response_code(419);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'CSRF token mismatch.', 'code' => 419]);
                exit;
            }
        }
    }
}
