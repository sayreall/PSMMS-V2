<?php

namespace App\Models;

class AdminModel extends Model
{
    protected string $table = 'admins';
    protected array $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'position',
        'area',
        'contact_no',
        'employee_id',
        'department',
        'company_email',
        'email',
        'profile_picture',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createAdmin(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
