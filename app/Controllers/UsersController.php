<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Helpers\Upload;
use App\Models\ActivityLogModel;
use App\Models\UserModel;
use PDO;

class UsersController extends BaseController
{
    private UserModel $userModel;
    private ActivityLogModel $activityLogModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->middleware[] = \App\Middleware\AdminMiddleware::class;
    }

    public function index(): string
    {
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 15);
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $sort = trim($_GET['sort'] ?? 'id');
        $order = trim($_GET['order'] ?? 'DESC');

        $where = [];
        if ($status) $where['status'] = $status;
        if ($role) $where['role'] = $role;

        $users = $this->getFilteredUsers($page, $perPage, $search, $where, $sort, $order);

        return $this->render('users.index', [
            'title' => 'Users Management',
            'users' => $users['data'],
            'pagination' => [
                'total' => $users['total'],
                'page' => $users['page'],
                'per_page' => $users['per_page'],
                'pages' => $users['pages'],
            ],
            'search' => $search,
            'status' => $status,
            'role' => $role,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    public function getFilteredUsers(int $page, int $perPage, string $search, array $where, string $sort, string $order): array
    {
        $db = \App\Config\Database::getInstance();

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM users WHERE 1=1";
        $countSql = "SELECT FOUND_ROWS()";
        $params = [];

        if ($search) {
            $sql .= " AND (name LIKE :search OR email LIKE :search OR company_email LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (isset($where['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $where['status'];
        }

        if (isset($where['role'])) {
            $sql .= " AND role = :role";
            $params[':role'] = $where['role'];
        }

        $allowedSort = ['id', 'name', 'email', 'company_email', 'role', 'status', 'created_at'];
        $sort = in_array($sort, $allowedSort) ? $sort : 'id';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY `$sort` $order";

        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT :perPage OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalStmt = $db->prepare($countSql);
        $totalStmt->execute();
        $total = (int)$totalStmt->fetchColumn();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => (int)ceil($total / $perPage),
        ];
    }

    public function create(): string
    {
        return $this->render('users.create', [
            'title' => 'Create User',
            'errors' => Session::getFlash('errors'),
            'old' => Session::getFlash('old'),
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $name = $this->composeFullName($first_name, $middle_name, $last_name);
        $email = trim($data['email'] ?? '');
        $company_email = trim($data['company_email'] ?? '');
        $password = $data['password'] ?? '';
        $role = trim($data['role'] ?? 'accounting');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'first_name' => 'required|min:2|max:100',
            'middle_name' => 'max:100',
            'last_name' => 'required|min:2|max:100',
            'email'    => 'required|email|unique:users.email',
            'company_email' => 'required|email|unique:users.company_email',
            'password' => 'required|min:8',
            'role'     => 'required|in:accounting,asm_manager,admin,head_manager,super_admin',
            'status'   => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation)->validate(compact('first_name', 'middle_name', 'last_name', 'email', 'company_email', 'password', 'role', 'status'), $rules);

        if (!$validator->passes()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', compact('first_name', 'middle_name', 'last_name', 'email', 'company_email', 'role', 'status'));
            $this->redirectBack();
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $userId = $this->userModel->createUser([
            'name' => $name,
            'first_name' => $first_name,
            'middle_name' => $middle_name ?: null,
            'last_name' => $last_name,
            'email' => $email,
            'company_email' => $company_email,
            'password' => $hashedPassword,
            'role' => $role,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'user_create', "Created user: $name");

        Session::flash('message', 'User created successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('users')]);
        }

        $this->redirect(App::url('users'));
    }

    public function edit(int $id): string
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            Session::flash('errors', ['general' => ['User not found.']]);
            $this->redirect(App::url('users'));
        }

        return $this->render('users.edit', [
            'title' => 'Edit User',
            'user' => $user,
            'errors' => Session::getFlash('errors'),
            'old' => Session::getFlash('old'),
        ]);
    }

    public function update(int $id): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $name = $this->composeFullName($first_name, $middle_name, $last_name);
        $email = trim($data['email'] ?? '');
        $company_email = trim($data['company_email'] ?? '');
        $role = trim($data['role'] ?? 'accounting');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'first_name' => 'required|min:2|max:100',
            'middle_name' => 'max:100',
            'last_name' => 'required|min:2|max:100',
            'email'  => 'required|email',
            'company_email' => 'required|email',
            'role'   => 'required|in:accounting,asm_manager,admin,head_manager,super_admin',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation)->validate(compact('first_name', 'middle_name', 'last_name', 'email', 'company_email', 'role', 'status'), $rules);

        if (!$validator->passes()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', compact('first_name', 'middle_name', 'last_name', 'email', 'company_email', 'role', 'status'));
            $this->redirectBack();
        }

        $this->userModel->update($id, [
            'name' => $name,
            'first_name' => $first_name,
            'middle_name' => $middle_name ?: null,
            'last_name' => $last_name,
            'email' => $email,
            'company_email' => $company_email,
            'role' => $role,
            'status' => $status,
        ]);
        (new ActivityLogModel())->log(Auth::id(), 'user_update', "Updated user ID: $id");

        Session::flash('message', 'User updated successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('users')]);
        }

        $this->redirect(App::url('users'));
    }

    public function delete(int $id): void
    {
        Csrf::verify();

        $user = $this->userModel->find($id);

        if (!$user) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse(['error' => ['User not found.']], 404);
            }
            $this->redirectBack();
        }

        // Prevent self-deletion
        if ((int)$id === Auth::id()) {
            Session::flash('errors', ['general' => ['You cannot delete your own account.']]);
            $this->redirectBack();
        }

        $this->userModel->delete($id);
        (new ActivityLogModel())->log(Auth::id(), 'user_delete', "Deleted user ID: $id");

        Session::flash('message', 'User deleted successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json(['success' => true, 'redirect' => App::url('users')]);
        }

        $this->redirect(App::url('users'));
    }

    private function composeFullName(string $first, string $middle, string $last): string
    {
        return trim(preg_replace('/\s+/', ' ', trim("$first $middle $last")) ?? '');
    }
}
