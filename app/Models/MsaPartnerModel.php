<?php

namespace App\Models;

class MsaPartnerModel extends Model
{
    protected string $table = 'msa_partners';
    protected array $fillable = [
        'user_id',
        'company_name',
        'username',
        'contact_no',
        'address',
        'installer',
        'msa_type',
        'email',
        'profile_picture',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createMsaPartner(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
