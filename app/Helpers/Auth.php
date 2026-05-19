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
    private const ACTIVE_STATUSES = ['active', 'approved'];

    private static function normalizeLegacyMsaStatus(array $user): array
    {
        $role = strtolower(trim((string)($user['role'] ?? '')));
        $status = strtolower(trim((string)($user['status'] ?? '')));
        if ($role !== 'msa_partners' || $status !== 'pending') {
            return $user;
        }

        $db = Database::getInstance();
        $userId = (int)($user['id'] ?? 0);
        $email = trim((string)($user['email'] ?? ''));

        $partner = [];
        if ($userId > 0) {
            $byUserId = $db->prepare("
                SELECT status
                FROM msa_partners
                WHERE user_id = :user_id
                ORDER BY id DESC
                LIMIT 1
            ");
            $byUserId->execute([':user_id' => $userId]);
            $partner = $byUserId->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        if (empty($partner) && $email !== '') {
            $byEmail = $db->prepare("
                SELECT status
                FROM msa_partners
                WHERE email = :email
                ORDER BY id DESC
                LIMIT 1
            ");
            $byEmail->execute([':email' => $email]);
            $partner = $byEmail->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $partnerStatus = strtolower(trim((string)($partner['status'] ?? '')));

        if (in_array($partnerStatus, self::ACTIVE_STATUSES, true)) {
            $update = $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE id = :id AND role = 'msa_partners'");
            $update->execute([
                ':id' => $userId,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);
            $user['status'] = 'active';
        }

        return $user;
    }

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
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email OR company_email = :company_email LIMIT 1");
        $stmt->execute([
            ':email' => $credentials['email'],
            ':company_email' => $credentials['email'],
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_array($user)) {
            $user = self::normalizeLegacyMsaStatus($user);
        }

        if (!$user || !password_verify($credentials['password'], $user['password'])) {
            return false;
        }

        if (!in_array(strtolower(trim((string)($user['status'] ?? ''))), self::ACTIVE_STATUSES, true)) {
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
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email OR company_email = :company_email LIMIT 1");
        $stmt->execute([
            ':email' => $credentials['email'],
            ':company_email' => $credentials['email'],
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_array($user)) {
            $user = self::normalizeLegacyMsaStatus($user);
        }

        if (!$user || !password_verify($credentials['password'], $user['password'])) {
            return null;
        }

        if (!in_array(strtolower(trim((string)($user['status'] ?? ''))), self::ACTIVE_STATUSES, true)) {
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
