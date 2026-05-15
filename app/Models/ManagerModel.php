<?php

namespace App\Models;

class ManagerModel extends Model
{
    protected string $table = 'managers';
    protected array $fillable = [
        'user_id',
        'manager_name',
        'position',
        'contact_no',
        'company_email',
        'email',
        'profile_picture',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createManager(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
