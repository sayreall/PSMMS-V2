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

    public function getForTable(?int $regionId = null): array
    {
        $where = '';
        $params = [];

        if ($regionId !== null && $regionId > 0) {
            $where = 'WHERE p.region_id = :region_id';
            $params[':region_id'] = $regionId;
        }

        $sql = "
            SELECT p.id, p.region_id, p.province_code, r.region_name AS region, r.region_code, p.province_name AS province
            FROM provinces p
            INNER JOIN regions r ON r.id = p.region_id
            {$where}
            ORDER BY r.region_name ASC, p.province_name ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getForSelect(?int $regionId = null): array
    {
        return $this->getForTable($regionId);
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
