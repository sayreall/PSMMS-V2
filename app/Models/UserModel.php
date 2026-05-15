<?php

namespace App\Models;

use PDO;

class UserModel extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'first_name', 'middle_name', 'last_name', 'email', 'company_email', 'password', 'role', 'status', 'avatar', 'created_at', 'updated_at'];

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function createUser(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($id, $data);
    }

    public function getActiveUsers(): array
    {
        return $this->all(['*'], ['status' => 'active'], ['created_at' => 'DESC']);
    }

    public function getStats(): array
    {
        return [
            'total'   => $this->count(),
            'active'  => $this->count(['status' => 'active']),
            'inactive'=> $this->count(['status' => 'inactive']),
            'admins'  => $this->count(['role' => 'admin']),
            'by_month'=> $this->getMonthlyRegistrations(),
        ];
    }

    public function getMonthlyRegistrations(): array
    {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
                FROM users
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
