<?php

namespace App\Models;

class DispatchStatusModel extends Model
{
    protected string $table = 'dispatch_statuses';
    protected array $fillable = [
        'dispatch_status',
        'color',
        'created_at',
        'updated_at',
    ];

    public function createDispatchStatus(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
