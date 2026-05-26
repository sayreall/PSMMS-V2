<?php

namespace App\Models\Users\SmeSales;

use App\Models\Model;

class AgentCodeModel extends Model
{
    protected string $table = 'agent_codes';
    protected array $fillable = [
        'sales_category_id',
        'sales_agent_id',
        'agent_code',
        'status',
        'validation',
        'created_at',
        'updated_at',
    ];

    public function createAgentCode(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
