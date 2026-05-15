<?php

namespace App\Controllers\Api;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\UserModel;

class AuthController
{
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_LOCKOUT_SECONDS = 300;

    public function login(): void
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?? [];

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $throttleKey = $this->throttleKey($email);

        if ($this->isLoginThrottled($throttleKey)) {
            $waitSeconds = max(1, (int) (Session::get($throttleKey)['locked_until'] - time()));
            Validation::jsonResponse(['email' => ["Too many login attempts. Try again in {$waitSeconds} seconds."]], 429);
        }

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];

        $validator = (new Validation)->validate(
            ['email' => $email, 'password' => $password],
            $rules
        );

        if (!$validator->passes()) {
            Validation::jsonResponse($validator->errors());
        }

        $token = Auth::attemptWithJWT(['email' => $email, 'password' => $password]);
        if (!$token) {
            $this->recordFailedLogin($throttleKey);
            Validation::jsonResponse(['email' => ['Invalid credentials.']], 401);
        }
        Session::remove($throttleKey);

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);
        if ($user) {
            unset($user['password']);
        }

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => $user,
        ]);
        exit;
    }

    private function throttleKey(string $email): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $normalized = strtolower($email);
        return 'api_login_throttle_' . hash('sha256', $normalized . '|' . $ip);
    }

    private function isLoginThrottled(string $key): bool
    {
        $state = Session::get($key);
        if (!is_array($state)) {
            return false;
        }

        $lockedUntil = (int) ($state['locked_until'] ?? 0);
        if ($lockedUntil <= time()) {
            Session::remove($key);
            return false;
        }

        return true;
    }

    private function recordFailedLogin(string $key): void
    {
        $state = Session::get($key, ['attempts' => 0, 'locked_until' => 0]);
        $attempts = (int) ($state['attempts'] ?? 0) + 1;
        $lockedUntil = 0;

        if ($attempts >= self::LOGIN_MAX_ATTEMPTS) {
            $lockedUntil = time() + self::LOGIN_LOCKOUT_SECONDS;
            $attempts = 0;
        }

        Session::set($key, [
            'attempts' => $attempts,
            'locked_until' => $lockedUntil,
        ]);
    }
}
