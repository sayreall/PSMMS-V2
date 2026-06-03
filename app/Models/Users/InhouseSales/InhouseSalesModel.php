<?php

namespace App\Models\Users\InhouseSales;

use App\Models\Model;

class InhouseSalesModel extends Model
{
    protected string $table = 'inhouse_sales';
    protected array $fillable = [
        'inhouse_id',
        'create_at',
        'user_id',
        'user_type',
        'sales_manager',
        'first_name',
        'middle_name',
        'last_name',
        'contact',
        'email',
        'password',
        'photos',
        'address',
        'sales_category',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createInhouseSales(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['create_at'] = $data['create_at'] ?? $now;
        $data['created_at'] = $data['created_at'] ?? $now;
        $data['updated_at'] = $data['updated_at'] ?? $now;
        $data['user_type'] = $data['user_type'] ?? 'inhouse_sales';

        $id = $this->create($data);
        $this->update($id, ['inhouse_id' => $data['inhouse_id'] ?? $id]);

        return $id;
    }
}
