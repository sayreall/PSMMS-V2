<?php

namespace App\Models;

use PDO;

class UserModel extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'first_name', 'middle_name', 'last_name', 'email', 'company_email', 'contact_no', 'password', 'role', 'status', 'avatar', 'created_at', 'updated_at'];
    private ?array $tableColumnsCache = null;

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function createUser(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($this->filterExistingColumns($data));
    }

    public function updateUser(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($id, $this->filterExistingColumns($data));
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

    private function filterExistingColumns(array $data): array
    {
        $columns = $this->getTableColumns();
        return array_intersect_key($data, array_flip($columns));
    }

    private function getTableColumns(): array
    {
        if ($this->tableColumnsCache !== null) {
            return $this->tableColumnsCache;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM `{$this->table}`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->tableColumnsCache = array_map(static fn(array $row) => (string)$row['Field'], $rows);

        return $this->tableColumnsCache;
    }
}
