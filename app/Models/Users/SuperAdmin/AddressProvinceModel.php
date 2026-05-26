<?php

namespace App\Models\Users\SuperAdmin;

use App\Models\Model;

class AddressProvinceModel extends Model
{
    protected string $table = 'provinces';
    protected array $fillable = [
        'region_id',
        'province_name',
        'province_code',
        'status',
    ];

    public function getForTable(): array
    {
        $sql = "
            SELECT p.id, r.region_name AS region, p.province_name AS province
            FROM provinces p
            INNER JOIN regions r ON r.id = p.region_id
            ORDER BY r.region_name ASC, p.province_name ASC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function findByRegionAndName(int $regionId, string $provinceName): ?array
    {
        $sql = "SELECT id, region_id, province_name FROM provinces WHERE region_id = :region_id AND province_name = :province_name LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':region_id' => $regionId,
            ':province_name' => $provinceName,
        ]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

