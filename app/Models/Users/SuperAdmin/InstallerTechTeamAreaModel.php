<?php

namespace App\Models\Users\SuperAdmin;

use App\Models\Model;

class InstallerTechTeamAreaModel extends Model
{
    protected string $table = 'installer_tech_team_areas';
    protected array $fillable = [
        'area',
        'team',
        'validation_status',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createTeamArea(array $data): int
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
            CREATE TABLE IF NOT EXISTS installer_tech_team_areas (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                area VARCHAR(150) NOT NULL,
                team VARCHAR(100) NOT NULL,
                validation_status ENUM('approved','pending','declined') NOT NULL DEFAULT 'pending',
                status ENUM('active','inactive') NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX area_index (area),
                INDEX team_index (team),
                INDEX validation_status_index (validation_status),
                INDEX status_index (status),
                INDEX created_at_index (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        $this->db->exec($sql);
    }
}
