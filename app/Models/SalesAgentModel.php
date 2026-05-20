<?php

namespace App\Models;

class SalesAgentModel extends Model
{
    protected string $table = 'sales_agents';
    protected array $fillable = [
        'sales_category_id',
        'agent_name',
        'status',
        'validation',
        'created_at',
        'updated_at',
    ];

    public function createSalesAgent(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
