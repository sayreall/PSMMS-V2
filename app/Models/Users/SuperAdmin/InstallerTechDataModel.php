<?php

namespace App\Models\Users\SuperAdmin;

use App\Models\Model;

class InstallerTechDataModel extends Model
{
    protected string $table = 'installer_tech_data';
    protected array $fillable = [
        'installer_name',
        'full_name',
        'type',
        'category',
        'area',
        'validation_status',
        'check_surf2sawa',
        'check_fiberx',
        'check_bida',
        'check_sme',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createTechData(array $data): int
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
            CREATE TABLE IF NOT EXISTS installer_tech_data (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                installer_name VARCHAR(150) NOT NULL,
                full_name VARCHAR(150) NOT NULL,
                type VARCHAR(50) NOT NULL,
                category VARCHAR(100) NOT NULL,
                area VARCHAR(150) NOT NULL,
                validation_status ENUM('approved','pending','declined') NOT NULL DEFAULT 'pending',
                check_surf2sawa TINYINT(1) NOT NULL DEFAULT 0,
                check_fiberx TINYINT(1) NOT NULL DEFAULT 0,
                check_bida TINYINT(1) NOT NULL DEFAULT 0,
                check_sme TINYINT(1) NOT NULL DEFAULT 0,
                status ENUM('active','inactive') NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX installer_name_index (installer_name),
                INDEX validation_status_index (validation_status),
                INDEX area_index (area),
                INDEX status_index (status),
                INDEX created_at_index (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        $this->db->exec($sql);
        $this->db->exec("ALTER TABLE installer_tech_data ADD COLUMN IF NOT EXISTS full_name VARCHAR(150) NOT NULL DEFAULT '' AFTER installer_name");
        $this->db->exec("ALTER TABLE installer_tech_data ADD COLUMN IF NOT EXISTS type VARCHAR(50) NOT NULL DEFAULT '' AFTER full_name");
        $this->db->exec("ALTER TABLE installer_tech_data ADD COLUMN IF NOT EXISTS category VARCHAR(100) NOT NULL DEFAULT '' AFTER type");
        $this->db->exec("ALTER TABLE installer_tech_data ADD COLUMN IF NOT EXISTS validation_status ENUM('approved','pending','declined') NOT NULL DEFAULT 'pending' AFTER area");
        $this->db->exec("ALTER TABLE installer_tech_data ADD COLUMN IF NOT EXISTS check_surf2sawa TINYINT(1) NOT NULL DEFAULT 0 AFTER validation_status");
        $this->db->exec("ALTER TABLE installer_tech_data ADD COLUMN IF NOT EXISTS check_fiberx TINYINT(1) NOT NULL DEFAULT 0 AFTER check_surf2sawa");
        $this->db->exec("ALTER TABLE installer_tech_data ADD COLUMN IF NOT EXISTS check_bida TINYINT(1) NOT NULL DEFAULT 0 AFTER check_fiberx");
        $this->db->exec("ALTER TABLE installer_tech_data ADD COLUMN IF NOT EXISTS check_sme TINYINT(1) NOT NULL DEFAULT 0 AFTER check_bida");
    }
}
