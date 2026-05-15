<?php

namespace App\Config;

use Dotenv\Dotenv;

class App
{
    public static string $name;
    public static string $url;
    public static bool $debug;
    public static string $env;
    public static string $locale;
    public static string $timezone;
    public static string $sessionName;
    public static int $sessionLifetime;
    public static string $jwtSecret;
    public static string $jwtAlgorithm;
    public static int $jwtExpiration;

    public static function init(): void
    {
        $rootPath = dirname(__DIR__, 2);
        $dotenv = Dotenv::createImmutable($rootPath);
        $dotenv->safeLoad();

        self::$name           = $_ENV['APP_NAME'] ?? 'PSMMS Dashboard';
        self::$url            = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/');
        self::$debug          = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
        self::$env            = $_ENV['APP_ENV'] ?? 'production';
        self::$locale         = $_ENV['APP_LOCALE'] ?? 'en';
        self::$timezone       = $_ENV['APP_TIMEZONE'] ?? 'UTC';
        self::$sessionName    = $_ENV['SESSION_NAME'] ?? 'PSMMS_SESSION';
        self::$sessionLifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);
        self::$jwtSecret      = $_ENV['JWT_SECRET'] ?? '';
        self::$jwtAlgorithm   = $_ENV['JWT_ALGORITHM'] ?? 'HS256';
        self::$jwtExpiration  = (int)($_ENV['JWT_EXPIRATION'] ?? 3600);

        date_default_timezone_set(self::$timezone);
        session_name(self::$sessionName);

        if (self::$debug) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
            error_reporting(0);
        }
    }

    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function isApiRequest(): bool
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
        if (str_starts_with($uri, '/api')) {
            return true;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return str_contains($contentType, 'application/json')
            || str_contains($accept, 'application/json');
    }

    public static function basePath(string $path = ''): string
    {
        return dirname(__DIR__, 2) . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/') : '');
    }

    public static function publicPath(string $path = ''): string
    {
        return self::basePath('public') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/') : '');
    }

    public static function url(string $path = ''): string
    {
        return self::$url . ($path ? '/' . ltrim($path, '/') : '');
    }
}