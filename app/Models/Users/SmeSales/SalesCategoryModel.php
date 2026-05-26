<?php

namespace App\Models\Users\SmeSales;

use App\Models\Model;

class SalesCategoryModel extends Model
{
    protected string $table = 'sales_categories';
    protected array $fillable = [
        'sales_category',
        'sales_manager',
        'type',
        'tl_status',
        'validation',
        'created_at',
        'updated_at',
    ];

    public function createSalesCategory(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
