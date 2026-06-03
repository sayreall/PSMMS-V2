<?php

namespace App\Models\Users\AsmManager;

use App\Models\Model;

class ManagerModel extends Model
{
    protected string $table = 'managers';
    protected array $fillable = [
        'manager_id',
        'create_at',
        'user_id',
        'user_type',
        'manager_name',
        'first_name',
        'middle_name',
        'last_name',
        'sales_manager',
        'position',
        'contact_no',
        'contact',
        'company_email',
        'password',
        'email',
        'photos',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createManager(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['create_at'] = $data['create_at'] ?? $data['created_at'];
        $data['user_type'] = $data['user_type'] ?? 'asm_manager';
        $data['manager_name'] = $data['manager_name'] ?? $this->composeFullName($data);
        $data['sales_manager'] = $data['sales_manager'] ?? $data['manager_name'];
        $data['contact'] = $data['contact'] ?? ($data['contact_no'] ?? null);
        $data['photos'] = $data['photos'] ?? null;

        $id = $this->create($data);

        if (empty($data['manager_id'])) {
            $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `manager_id` = :manager_id WHERE `id` = :id LIMIT 1");
            $stmt->execute([
                ':manager_id' => $id,
                ':id' => $id,
            ]);
        }

        return $id;
    }

    private function composeFullName(array $data): string
    {
        return trim(preg_replace('/\s+/', ' ', trim(($data['first_name'] ?? '') . ' ' . ($data['middle_name'] ?? '') . ' ' . ($data['last_name'] ?? ''))) ?? '');
    }
}
