<?php

namespace App\Models;

class AsmNameModel extends Model
{
    protected string $table = 'asm_names';
    protected array $fillable = [
        'name',
        'validation_status',
        'check_surf2sawa',
        'check_fiberx',
        'check_bida',
        'check_sme',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createAsmName(array $data): int
    {
        $this->ensureTable();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }

    public function all(array $columns = ['*'], array $where = [], array $order = []): array
    {
        $this->ensureTable();
        return parent::all($columns, $where, $order);
    }

    private function ensureTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS asm_names (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                validation_status ENUM('approved','pending','declined') NOT NULL DEFAULT 'pending',
                check_surf2sawa TINYINT(1) NOT NULL DEFAULT 0,
                check_fiberx TINYINT(1) NOT NULL DEFAULT 0,
                check_bida TINYINT(1) NOT NULL DEFAULT 0,
                check_sme TINYINT(1) NOT NULL DEFAULT 0,
                status ENUM('active','inactive') NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX name_index (name),
                INDEX validation_status_index (validation_status),
                INDEX status_index (status),
                INDEX created_at_index (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        $this->db->exec($sql);
    }
}
