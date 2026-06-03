<?php

namespace App\Models\Users\SuperAdmin;

use App\Models\Model;

class AdminModel extends Model
{
    protected string $table = 'admins';
    protected array $fillable = [
        'admin_id',
        'create_at',
        'user_id',
        'user_type',
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'position',
        'area',
        'contact_no',
        'contact',
        'address',
        'employee_id',
        'department',
        'company_email',
        'password',
        'email',
        'photos',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createAdmin(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['create_at'] = $data['create_at'] ?? $data['created_at'];
        $data['user_type'] = $data['user_type'] ?? 'admin';
        $data['username'] = $data['username'] ?? trim(preg_replace('/\s+/', ' ', trim(($data['first_name'] ?? '') . ' ' . ($data['middle_name'] ?? '') . ' ' . ($data['last_name'] ?? ''))) ?? '');
        $data['contact'] = $data['contact'] ?? ($data['contact_no'] ?? null);
        $data['photos'] = $data['photos'] ?? null;

        $id = $this->create($data);

        if (empty($data['admin_id'])) {
            $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `admin_id` = :admin_id WHERE `id` = :id LIMIT 1");
            $stmt->execute([
                ':admin_id' => $id,
                ':id' => $id,
            ]);
        }

        return $id;
    }
}
