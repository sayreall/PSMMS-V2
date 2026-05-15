<?php

namespace App\Middleware;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiAuthMiddleware
{
    public function handle(): ?string
    {
        Auth::init();

        if (Auth::check()) {
            return null;
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            Validation::jsonResponse(['auth' => ['Missing bearer token.']], 401);
        }

        $token = trim($matches[1]);
        if ($token === '') {
            Validation::jsonResponse(['auth' => ['Invalid bearer token.']], 401);
        }

        try {
            if (empty(App::$jwtSecret)) {
                Validation::jsonResponse(['auth' => ['JWT secret not configured.']], 500);
            }

            $payload = JWT::decode($token, new Key(App::$jwtSecret, App::$jwtAlgorithm));
            $userId = (int) ($payload->sub ?? 0);

            if ($userId <= 0) {
                Validation::jsonResponse(['auth' => ['Invalid token subject.']], 401);
            }

            $userModel = new UserModel();
            $user = $userModel->find($userId);
            if (!$user || ($user['status'] ?? '') !== 'active') {
                Validation::jsonResponse(['auth' => ['User is inactive or missing.']], 401);
            }

            unset($user['password']);
            Session::set('user', $user);
            Auth::init();
        } catch (\Throwable $e) {
            Validation::jsonResponse(['auth' => ['Invalid or expired token.']], 401);
        }

        return null;
    }
}
