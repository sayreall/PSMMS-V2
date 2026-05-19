<?php

namespace App\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    private function __construct() {}

    public static function loadEnv(string $path = null): void
    {
        $rootPath = $path ?? dirname(__DIR__, 2);

        // App::init() already attempts to load .env. Avoid hard-failing here
        // if .env contains malformed lines; use existing environment values.
        if (
            !isset($_ENV['DB_HOST']) ||
            !isset($_ENV['DB_NAME']) ||
            !isset($_ENV['DB_USER']) ||
            !isset($_ENV['DB_CHARSET'])
        ) {
            $dotenv = Dotenv::createImmutable($rootPath);
            try {
                $dotenv->safeLoad();
            } catch (\Throwable $e) {
                error_log('Dotenv load warning (Database): ' . $e->getMessage());
            }
        }

        $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_CHARSET'];
        foreach ($required as $key) {
            if (!isset($_ENV[$key]) || trim((string)$_ENV[$key]) === '') {
                throw new \RuntimeException("Missing required environment variable: {$key}");
            }
        }

        self::$config = [
            'host'     => $_ENV['DB_HOST'],
            'dbname'   => $_ENV['DB_NAME'],
            'user'     => $_ENV['DB_USER'],
            'pass'     => $_ENV['DB_PASS'] ?? '',
            'port'     => $_ENV['DB_PORT'] ?? 3306,
            'charset'  => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
        ];
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    self::$config['host'],
                    self::$config['port'],
                    self::$config['dbname'],
                    self::$config['charset']
                );

                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_PERSISTENT         => false,
                ];

                self::$instance = new PDO($dsn, self::$config['user'], self::$config['pass'], $options);
            } catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                throw new PDOException('Unable to connect to the database.', 0, $e);
            }
        }

        return self::$instance;
    }

    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getInstance()->rollBack();
    }

    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}
