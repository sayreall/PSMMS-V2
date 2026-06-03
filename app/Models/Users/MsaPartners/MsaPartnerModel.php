<?php

namespace App\Models\Users\MsaPartners;

use App\Models\Model;

class MsaPartnerModel extends Model
{
    protected string $table = 'msa_partners';
    protected array $fillable = [
        'partners_id',
        'create_at',
        'user_id',
        'user_type',
        'sales_manager',
        'company_name',
        'first_name',
        'middle_name',
        'last_name',
        'contact',
        'email',
        'password',
        'photos',
        'area_type',
        'address',
        'sales_category',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createMsaPartner(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['create_at'] = $data['create_at'] ?? $now;
        $data['created_at'] = $data['created_at'] ?? $now;
        $data['updated_at'] = $data['updated_at'] ?? $now;
        $data['user_type'] = $data['user_type'] ?? 'msa_partners';

        $id = $this->create($data);
        $this->update($id, ['partners_id' => $data['partners_id'] ?? $id]);

        return $id;
    }
}
