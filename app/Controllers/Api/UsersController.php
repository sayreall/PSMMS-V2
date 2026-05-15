<?php

namespace App\Controllers\Api;

use App\Config\Database;
use App\Helpers\Auth;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\UserModel;
use PDO;

class UsersController
{
    private UserModel $userModel;
    private ActivityLogModel $activityLogModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, (int) ($_GET['per_page'] ?? 15));
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $sort = trim($_GET['sort'] ?? 'id');
        $order = trim($_GET['order'] ?? 'DESC');

        $result = $this->getFilteredUsers($page, $perPage, $search, $status, $role, $sort, $order);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['success' => true] + $result);
        exit;
    }

    public function store(): void
    {
        if (!Auth::hasRole('admin')) {
            Validation::jsonResponse(['auth' => ['Forbidden.']], 403);
        }

        $data = $this->requestData();
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $role = trim($data['role'] ?? 'accounting');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users.email',
            'password' => 'required|min:8',
            'role' => 'required|in:accounting,asm_manager,admin,head_manager,super_admin',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation)->validate(
            compact('name', 'email', 'password', 'role', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Validation::jsonResponse($validator->errors());
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $userId = $this->userModel->createUser([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'status' => $status,
        ]);

        $this->activityLogModel->log((int) Auth::id(), 'user_create', "Created user: $name");

        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $userId]);
        exit;
    }

    public function update(int $id): void
    {
        if (!Auth::hasRole('admin')) {
            Validation::jsonResponse(['auth' => ['Forbidden.']], 403);
        }

        $data = $this->requestData();
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = trim($data['role'] ?? 'accounting');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'role' => 'required|in:accounting,asm_manager,admin,head_manager,super_admin',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation)->validate(
            compact('name', 'email', 'role', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Validation::jsonResponse($validator->errors());
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            Validation::jsonResponse(['error' => ['User not found.']], 404);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            Validation::jsonResponse(['email' => ['Email already taken.']], 422);
        }

        $this->userModel->update($id, compact('name', 'email', 'role', 'status'));
        $this->activityLogModel->log((int) Auth::id(), 'user_update', "Updated user ID: $id");

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function delete(int $id): void
    {
        if (!Auth::hasRole('admin')) {
            Validation::jsonResponse(['auth' => ['Forbidden.']], 403);
        }

        if ((int) $id === (int) Auth::id()) {
            Validation::jsonResponse(['error' => ['You cannot delete your own account.']], 400);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            Validation::jsonResponse(['error' => ['User not found.']], 404);
        }

        $this->userModel->delete($id);
        $this->activityLogModel->log((int) Auth::id(), 'user_delete', "Deleted user ID: $id");

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    private function requestData(): array
    {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        return is_array($json) ? $json : [];
    }

    private function getFilteredUsers(int $page, int $perPage, string $search, string $status, string $role, string $sort, string $order): array
    {
        $db = Database::getInstance();
        $params = [];
        $conditions = [];

        if ($search) {
            $conditions[] = '(name LIKE :search OR email LIKE :search)';
            $params[':search'] = "%$search%";
        }
        if ($status) {
            $conditions[] = 'status = :status';
            $params[':status'] = $status;
        }
        if ($role) {
            $conditions[] = 'role = :role';
            $params[':role'] = $role;
        }

        $where = $conditions ? ('WHERE ' . implode(' AND ', $conditions)) : '';
        $allowedSort = ['id', 'name', 'email', 'role', 'status', 'created_at'];
        $sort = in_array($sort, $allowedSort) ? $sort : 'id';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM users $where ORDER BY `$sort` $order LIMIT :perPage OFFSET :offset";
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $countSql = "SELECT COUNT(*) FROM users $where";
        $countStmt = $db->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => (int) ceil($total / $perPage),
        ];
    }
}
