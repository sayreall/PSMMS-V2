<?php

namespace App\Models\Users\SuperAdmin;

use App\Models\Model;

class AddressRegionModel extends Model
{
    protected string $table = 'regions';
    protected array $fillable = [
        'region_name',
        'region_code',
        'status',
    ];

    public function getForTable(): array
    {
        return $this->all(['id', 'region_name'], [], ['region_name' => 'ASC']);
    }

    public function findByName(string $name): ?array
    {
        return $this->findBy('region_name', $name, ['id', 'region_name']);
    }
}

