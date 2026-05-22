<?php

namespace App\Models;

class DispatchRemarksModel extends Model
{
    protected string $table = 'dispatch_remarks';
    protected array $fillable = [
        'dispatch_remarks',
        'created_at',
        'updated_at',
    ];

    public function createDispatchRemarks(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
