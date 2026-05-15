<?php

namespace App\Helpers;

use App\Config\Database;
use App\Config\App;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class Auth
{
    private static ?array $user = null;

    public static function init(): void
    {
        Session::init();
        if (self::$user === null) {
            self::$user = Session::get('user');
        }
    }

    public static function attempt(array $credentials): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $credentials['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($credentials['password'], $user['password'])) {
            return false;
        }

        if ($user['status'] !== 'active') {
            return false;
        }

        unset($user['password']);
        self::$user = $user;
        Session::set('user', $user);
        Session::regenerate();

        return true;
    }

    public static function attemptWithJWT(array $credentials): ?string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $credentials['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($credentials['password'], $user['password'])) {
            return null;
        }

        if ($user['status'] !== 'active') {
            return null;
        }

        unset($user['password']);
        $token = JWT::encode([
            'iss' => App::$url,
            'sub' => (string) $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + App::$jwtExpiration,
        ], App::$jwtSecret, App::$jwtAlgorithm);

        return $token;
    }

    public static function logout(): void
    {
        Session::destroy();
        self::$user = null;
    }

    public static function user(): ?array
    {
        return self::$user;
    }

    public static function id(): int|string|null
    {
        return self::$user['id'] ?? null;
    }

    public static function email(): ?string
    {
        return self::$user['email'] ?? null;
    }

    public static function role(): ?string
    {
        return self::$user['role'] ?? null;
    }

    public static function check(): bool
    {
        return self::$user !== null;
    }

    public static function hasRole(string $role): bool
    {
        if (!self::$user) return false;
        $roles = [
            'accounting' => 1,
            'asm_manager' => 2,
            'admin' => 3,
            'head_manager' => 4,
            'super_admin' => 5,
        ];
        return ($roles[self::$user['role']] ?? 0) >= ($roles[$role] ?? 0);
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
