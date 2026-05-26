<?php

namespace App\Models\Users\SuperAdmin;

use App\Models\Model;

class AddressMunicipalityModel extends Model
{
    protected string $table = 'municipalities';
    protected array $fillable = [
        'province_id',
        'municipality_name',
        'municipality_code',
        'zip_code',
        'status',
    ];

    public function getForTable(): array
    {
        $sql = "
            SELECT m.id, r.region_name AS region, p.province_name AS province, m.municipality_name AS municipality
            FROM municipalities m
            INNER JOIN provinces p ON p.id = m.province_id
            INNER JOIN regions r ON r.id = p.region_id
            ORDER BY r.region_name ASC, p.province_name ASC, m.municipality_name ASC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function findByProvinceAndName(int $provinceId, string $municipalityName): ?array
    {
        $sql = "SELECT id, province_id, municipality_name FROM municipalities WHERE province_id = :province_id AND municipality_name = :municipality_name LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':province_id' => $provinceId,
            ':municipality_name' => $municipalityName,
        ]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

