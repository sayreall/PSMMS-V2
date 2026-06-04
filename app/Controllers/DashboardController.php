<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\DashboardRouteResolver;
use App\Models\Users\SuperAdmin\DispatchStatusModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    private UserModel $userModel;
    private ActivityLogModel $activityLogModel;
    private DashboardRouteResolver $dashboardRouteResolver;
    private DispatchStatusModel $dispatchStatusModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->dashboardRouteResolver = new DashboardRouteResolver();
        $this->dispatchStatusModel = new DispatchStatusModel();
    }

    public function index(): string
    {
        $user = Auth::user();
        $target = $this->dashboardRouteResolver->resolvePath($user ?? []);
        $this->redirect(App::url($target));
    }

    public function roleDashboard(string $slug, string $section = ''): string
    {
        $user = Auth::user();
        $targetPath = $this->dashboardRouteResolver->resolvePath($user ?? []);
        $targetSlug = basename($targetPath);

        if ($slug !== $targetSlug) {
            $this->redirect(App::url($targetPath));
        }

        if ($section !== '') {
            $_GET['section'] = $section;
        }

        $stats = [
            'total_users'   => $this->userModel->count(),
            'active_users'  => $this->userModel->count(['status' => 'active']),
            'inactive_users'=> $this->userModel->count(['status' => 'inactive']),
            'admins'        => $this->userModel->count(['role' => 'admin']),
        ];

        $recentActivities = $this->activityLogModel->getRecentActivities(10);
        $monthlyRegistrations = $this->userModel->getMonthlyRegistrations();
        try {
            $dispatchStatuses = $this->dispatchStatusModel->all(
                ['id', 'dispatch_status', 'color'],
                [],
                ['id' => 'ASC']
            );
        } catch (\Throwable $e) {
            error_log('Dashboard dispatch status load failed: ' . $e->getMessage());
            $dispatchStatuses = [];
        }

        if ($slug === 'super-admin') {
            return $this->render('dashboard.index', [
                'title' => 'Dashboard',
                'user' => $user,
                'stats' => $stats,
                'recentActivities' => $recentActivities,
                'monthlyRegistrations' => $monthlyRegistrations,
            ]);
        }

        return $this->render('dashboard.role', [
            'title' => $this->dashboardTitle($slug),
            'user' => $user,
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'monthlyRegistrations' => $monthlyRegistrations,
            'dashboardSlug' => $slug,
            'dispatchStatuses' => $dispatchStatuses,
        ]);
    }

    public function getStats(): void
    {
        $userStats = $this->userModel->getStats();
        $activityStats = $this->activityLogModel->getActivityStats();

        $this->json([
            'success' => true,
            'data' => [
                'users' => $userStats,
                'activity' => $activityStats,
                'timestamp' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function profile(): string
    {
        $user = Auth::user();
        return $this->render('dashboard.profile', [
            'title' => 'My Profile',
            'user' => $user,
            'displayRole' => $this->dashboardRouteResolver->resolveDisplayRole($user ?? []),
        ]);
    }

    public function viewProfile(): string
    {
        $user = Auth::user();
        return $this->render('dashboard.view-profile', [
            'title' => 'View Profile',
            'user' => $user,
            'displayRole' => $this->dashboardRouteResolver->resolveDisplayRole($user ?? []),
        ]);
    }

    public function updateProfile(): void
    {
        Csrf::verify();

        $userId = Auth::id();
        $data = $this->requestData();
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');

        $rules = [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email',
        ];

        $validator = (new Validation)->validate(compact('name', 'email'), $rules);

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('errors', $validator->errors());
            $this->redirectBack();
        }

        // Check email uniqueness excluding current user
        $db = \App\Config\Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $userId]);

        if ($stmt->fetchColumn() > 0) {
            $errors = ['email' => ['Email already taken.']];
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($errors);
            }
            Session::flash('errors', $errors);
            $this->redirectBack();
        }

        $updatePayload = compact('name', 'email');

        if (isset($_FILES['avatar']) && ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $avatarPath = Upload::store($_FILES['avatar'], 'avatars', 'usr_');
            if ($avatarPath === null) {
                $errors = ['avatar' => ['Avatar upload failed. Please upload a valid image up to 5MB.']];
                Session::flash('errors', $errors);
                Session::flash('message', 'Unable to update avatar.');
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }

            $currentUser = $this->userModel->find($userId);
            if (!empty($currentUser['avatar'])) {
                Upload::delete($currentUser['avatar']);
            }
            $updatePayload['avatar'] = $avatarPath;
        }

        $this->userModel->update($userId, $updatePayload);
        (new ActivityLogModel())->log($userId, 'profile_update', 'User updated profile');

        // Refresh user session data
        $user = $this->userModel->find($userId);
        unset($user['password']);
        Session::set('user', $user);

        Session::flash('message', 'Profile updated successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'message' => 'Profile updated successfully.']);
        }

        $this->redirect(App::url('dashboard/profile'));
    }

    public function updatePassword(): void
    {
        Csrf::verify();

        $userId = Auth::id();
        $data = $this->requestData();
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        $user = $this->userModel->find($userId);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $errors = ['current_password' => ['Current password is incorrect.']];
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($errors);
            }
            Session::flash('errors', $errors);
            $this->redirectBack();
        }

        $rules = [
            'new_password'      => 'required|min:8',
            'confirm_password'  => 'required|same:new_password',
        ];

        $validator = (new Validation)->validate(
            ['new_password' => $newPassword, 'confirm_password' => $confirmPassword],
            $rules
        );

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('errors', $validator->errors());
            $this->redirectBack();
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->userModel->update($userId, ['password' => $hashed]);
        (new ActivityLogModel())->log($userId, 'password_change', 'User changed password');

        Session::flash('message', 'Password changed successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'message' => 'Password changed successfully.']);
        }

        $this->redirect(App::url('dashboard/profile'));
    }

    private function dashboardTitle(string $slug): string
    {
        $map = [
            'super-admin' => 'Super Admin Dashboard',
            'admin' => 'Admin Dashboard',
            'admin-sales-admin' => 'Sales Admin Dashboard',
            'admin-dispatcher' => 'Dispatcher Dashboard',
            'admin-tech-leaders' => 'Tech Leaders Dashboard',
            'admin-sales-team-leader' => 'Sales Team Leader Dashboard',
            'admin-validator' => 'Validator Dashboard',
            'admin-backend' => 'Backend Dashboard',
            'admin-marketing' => 'Marketing Dashboard',
            'admin-supervisor' => 'Supervisor Dashboard',
            'admin-product-business-manager' => 'Product & Business Manager Dashboard',
            'head-manager' => 'Head Manager Dashboard',
            'accounting' => 'Accounting Dashboard',
            'asm-manager' => 'ASM Manager Dashboard',
            'asm-super-manager' => 'Super Manager Dashboard',
            'asm-area-sales-manager' => 'Area Sales Manager Dashboard',
            'asm-head-manager' => 'Head Manager Dashboard',
            'inhouse-sales' => 'In-house Sales Dashboard',
            'msa-partners' => 'MSA Partners Dashboard',
            'sme-sales' => 'SME Sales Dashboard',
            'general' => 'Dashboard',
        ];

        return $map[$slug] ?? 'Dashboard';
    }
}
