<?php

namespace App\Models;

class PlanModel extends Model
{
    protected string $table = 'plans';
    protected array $fillable = [
        'product',
        'plan',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createPlan(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
