<?php

use Dotenv\Dotenv;

$rootPath = dirname(__DIR__, 2);

if (!class_exists(Dotenv::class)) {
    require_once $rootPath . '/vendor/autoload.php';
}

$dotenv = Dotenv::createImmutable($rootPath);
$dotenv->safeLoad();

$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_NAME'] ?? '';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';
$port = $_ENV['DB_PORT'] ?? 3306;
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

if (empty($dbName)) {
    echo "[WARN] DB_NAME not set. Skipping admin seed.\n";
    return;
}

try {
    if (!isset($pdo) || !$pdo instanceof PDO) {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbName;charset=$charset",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    $email = 'admin@psmms.local';
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);

    if ($stmt->fetchColumn()) {
        echo "[INFO] Admin user already exists.\n";
        return;
    }

    $hashed = password_hash('Admin@123', PASSWORD_BCRYPT, ['cost' => 12]);
    $now = date('Y-m-d H:i:s');

    $insert = $pdo->prepare('INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES (:name, :email, :password, :role, :status, :created_at, :updated_at)');
    $insert->execute([
        ':name' => 'System Admin',
        ':email' => $email,
        ':password' => $hashed,
        ':role' => 'super_admin',
        ':status' => 'active',
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);

    echo "[OK] Admin user created (admin@psmms.local / Admin@123).\n";
} catch (Throwable $e) {
    echo "[WARN] Admin seed failed: " . $e->getMessage() . "\n";
}
