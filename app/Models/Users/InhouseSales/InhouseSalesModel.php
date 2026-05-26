<?php

namespace App\Models\Users\InhouseSales;

use App\Models\Model;

class InhouseSalesModel extends Model
{
    protected string $table = 'inhouse_sales';
    protected array $fillable = [
        'user_id',
        'sales_manager',
        'sales_category',
        'first_name',
        'last_name',
        'employee_id',
        'contact_no',
        'email',
        'profile_picture',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createInhouseSales(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
