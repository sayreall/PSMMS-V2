<?php

namespace App\Models;

use App\Config\Database;
use PDO;

abstract class Model
{
    protected string $table;
    protected array $fillable = [];
    protected string $primaryKey = 'id';
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(array $columns = ['*'], array $where = [], array $order = []): array
    {
        $sql = "SELECT " . implode(', ', $columns) . " FROM `{$this->table}`";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                $placeholder = ":where_" . str_replace('.', '_', $column);
                $conditions[] = "`$column` = $placeholder";
                $params[$placeholder] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if (!empty($order)) {
            $orders = [];
            foreach ($order as $column => $direction) {
                $orders[] = "`$column` " . (strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC');
            }
            $sql .= " ORDER BY " . implode(', ', $orders);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id, array $columns = ['*']): ?array
    {
        $sql = "SELECT " . implode(', ', $columns) . " FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findBy(string $column, $value, array $columns = ['*']): ?array
    {
        $sql = "SELECT " . implode(', ', $columns) . " FROM `{$this->table}` WHERE `$column` = :value LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':value' => $value]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $filtered = $this->filterFillable($data);
        $columns = array_keys($filtered);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = "INSERT INTO `{$this->table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($filtered);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $filtered = $this->filterFillable($data);
        $sets = [];
        $params = [':id' => $id];

        foreach (array_keys($filtered) as $column) {
            $sets[] = "`$column` = :$column";
            $params[":$column"] = $filtered[$column];
        }

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $sets) . " WHERE `{$this->primaryKey}` = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function count(array $where = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                $placeholder = ":count_" . str_replace('.', '_', $column);
                $conditions[] = "`$column` = $placeholder";
                $params[$placeholder] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function search(string $searchTerm, array $columns, array $where = []): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];
        $conditions = $where;

        if (!empty($searchTerm)) {
            $searchConditions = [];
            foreach ($columns as $col) {
                $placeholder = ":search_" . $col;
                $searchConditions[] = "`$col` LIKE $placeholder";
                $params[$placeholder] = "%$searchTerm%";
            }
            $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paginate(int $page = 1, int $perPage = 15, array $where = [], string $orderBy = '', string $orderDir = 'DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM `{$this->table}`";
        $countSql = "SELECT COUNT(*) FROM `{$this->table}`";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                $placeholder = ":page_" . str_replace('.', '_', $column);
                $conditions[] = "`$column` = $placeholder";
                $params[$placeholder] = $value;
            }
            $whereClause = " WHERE " . implode(' AND ', $conditions);
            $sql .= $whereClause;
            $countSql .= $whereClause;
        }

        if ($orderBy) {
            $sql .= " ORDER BY `$orderBy` " . (strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC');
        }

        $sql .= " LIMIT :perPage OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $totalStmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $totalStmt->bindValue($key, $value);
        }
        $totalStmt->execute();
        $total = (int) $totalStmt->fetchColumn();

        return [
            'data'  => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page'  => $page,
            'per_page' => $perPage,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }
}