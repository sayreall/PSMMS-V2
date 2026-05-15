<?php

namespace App\Controllers\Api;

use App\Models\ActivityLogModel;
use App\Models\UserModel;

class DashboardController
{
    private UserModel $userModel;
    private ActivityLogModel $activityLogModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    public function stats(): void
    {
        $userStats = $this->userModel->getStats();
        $activityStats = $this->activityLogModel->getActivityStats();

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'users' => $userStats,
                'activity' => $activityStats,
                'timestamp' => date('Y-m-d H:i:s'),
            ],
        ]);
        exit;
    }
}
