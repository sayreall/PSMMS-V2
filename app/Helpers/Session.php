<?php

namespace App\Helpers;

use App\Config\App;

class Session
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');

            $isSecure = false;
            $appUrl = App::$url ?? '';
            if ($appUrl && str_starts_with($appUrl, 'https://')) {
                $isSecure = true;
            }
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                $isSecure = true;
            }

            session_set_cookie_params([
                'lifetime' => App::$sessionLifetime ?? 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function flash(string $key, $value): void
    {
        $_SESSION[$key] = $value;
        $_SESSION["__flash__$key"] = true;
    }

    public static function getFlash(string $key, $default = null)
    {
        if (isset($_SESSION["__flash__$key"])) {
            $value = $_SESSION[$key] ?? $default;
            unset($_SESSION[$key], $_SESSION["__flash__$key"]);
            return $value;
        }
        return $default;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrf(string $token): bool
    {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}