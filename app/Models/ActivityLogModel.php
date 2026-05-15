<?php

namespace App\Models;

use PDO;

class ActivityLogModel extends Model
{
    protected string $table = 'activity_logs';
    protected array $fillable = ['user_id', 'action', 'description', 'ip_address', 'user_agent', 'created_at'];

    public function log(int $userId, string $action, string $description = ''): int
    {
        return $this->create([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function getRecentActivities(int $limit = 20): array
    {
        $sql = "SELECT activity_logs.*, users.name as user_name
                FROM activity_logs
                LEFT JOIN users ON activity_logs.user_id = users.id
                ORDER BY activity_logs.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActivityStats(): array
    {
        $sql = "SELECT
                    COUNT(*) as total,
                    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as this_week
                FROM activity_logs";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}