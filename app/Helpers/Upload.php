<?php

namespace App\Helpers;

use App\Config\App;

class Upload
{
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    private const MAX_SIZE = 5242880; // 5MB

    public static function store(array $file, string $directory, string $prefix = ''): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if (!in_array($file['type'], self::ALLOWED_TYPES)) {
            return null;
        }

        if ($file['size'] > self::MAX_SIZE) {
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . bin2hex(random_bytes(16)) . '.' . $extension;

        $targetDir = App::basePath('public/uploads/' . $directory);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $destination = $targetDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $directory . '/' . $filename;
        }

        return null;
    }

    public static function delete(?string $path): bool
    {
        if (!$path) return false;
        $fullPath = App::basePath('public/' . $path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public static function url(?string $path): string
    {
        if (!$path) return '';
        return App::url('uploads/' . $path);
    }
}