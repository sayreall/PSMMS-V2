#!/usr/bin/env php
<?php
/**
 * Automated installation script for PSMMS Dashboard
 * Creates .env from .env.example and runs database migrations
 */

$rootPath = __DIR__;

echo "=============================================\n";
echo "  PSMMS Dashboard - Installation Script\n";
echo "=============================================\n\n";

// Check PHP version
$phpVersion = PHP_VERSION;
if (version_compare($phpVersion, '8.0.0', '<')) {
    die("[ERROR] PHP 8.0+ is required. Current version: $phpVersion\n");
}
echo "[OK] PHP version: $phpVersion\n";

// Check required extensions
$requiredExtensions = ['pdo_mysql', 'json', 'mbstring', 'openssl', 'curl', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        die("[ERROR] Required extension not loaded: $ext\n");
    }
}
echo "[OK] All required extensions loaded\n";

// Create .env if not exists
$envPath = $rootPath . '/.env';
$envExamplePath = $rootPath . '/.env.example';

if (!file_exists($envPath)) {
    if (file_exists($envExamplePath)) {
        copy($envExamplePath, $envPath);
        echo "[OK] Created .env file from .env.example\n";
    } else {
        die("[ERROR] .env.example not found at $envExamplePath\n");
    }
} else {
    echo "[INFO] .env file already exists\n";
}

// Load .env
require_once $rootPath . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($rootPath);
$dotenv->load();

// Test database connection
echo "\n--- Database Setup ---\n";
try {
    $dbHost = $_ENV['DB_HOST'];
    $dbName = $_ENV['DB_NAME'];
    $dbUser = $_ENV['DB_USER'];
    $dbPass = $_ENV['DB_PASS'] ?? '';
    $dbPort = $_ENV['DB_PORT'] ?? 3306;
    $dbCharset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

    $pdo = new PDO(
        "mysql:host=$dbHost;port=$dbPort;charset=$dbCharset",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "[OK] Database server connected\n";

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET $dbCharset COLLATE " . ($dbCharset === 'utf8mb4' ? 'utf8mb4_unicode_ci' : 'utf8_general_ci'));
    echo "[OK] Database `$dbName` ready\n";

    // Select database
    $pdo->exec("USE `$dbName`");

    // Run migrations
    $migrationsDir = $rootPath . '/database/migrations';
    if (is_dir($migrationsDir)) {
        echo "\n--- Running Migrations ---\n";
        $migrations = glob($migrationsDir . '/*.sql');
        sort($migrations);

        foreach ($migrations as $migration) {
            $sql = file_get_contents($migration);
            if (!empty(trim($sql))) {
                try {
                    $pdo->exec($sql);
                    $fileName = basename($migration);
                    echo "[OK] Migration applied: $fileName\n";
                } catch (PDOException $e) {
                    echo "[WARN] Migration failed: " . basename($migration) . " - " . $e->getMessage() . "\n";
                }
            }
        }
    }

    // Run seeds
    $seedsDir = $rootPath . '/database/seeds';
    if (is_dir($seedsDir)) {
        echo "\n--- Running Seeders ---\n";
        $seederFiles = glob($seedsDir . '/*.sql');
        sort($seederFiles);

        foreach ($seederFiles as $seeder) {
            $sql = file_get_contents($seeder);
            if (!empty(trim($sql))) {
                try {
                    $pdo->exec($sql);
                    $fileName = basename($seeder);
                    echo "[OK] Seeder applied: $fileName\n";
                } catch (PDOException $e) {
                    echo "[WARN] Seeder failed: " . basename($seeder) . " - " . $e->getMessage() . "\n";
                }
            }
        }
    }

    // Run PHP seeders (for dynamic data like hashed passwords)
    $phpSeeder = $seedsDir . '/seed_admin.php';
    if (file_exists($phpSeeder)) {
        echo "\n--- Running PHP Seeder ---\n";
        require $phpSeeder;
    }

    echo "\n=============================================\n";
    echo "  Installation Complete!\n";
    echo "=============================================\n";
    echo "\nNext steps:\n";
    echo "  1. Run: composer install\n";
    echo "  2. Start server: php -S localhost:8000 -t public\n";
    echo "  3. Open: http://localhost:8000\n";
    echo "\n";

} catch (PDOException $e) {
    die("[ERROR] Database connection failed: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("[ERROR] " . $e->getMessage() . "\n");
}