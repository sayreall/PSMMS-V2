<?php

namespace App\Controllers\Users\InhouseSales;

use App\Config\App;
use App\Controllers\BaseController;
use App\Config\Database;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\Users\InhouseSales\InhouseSalesModel;

class InhouseController extends BaseController
{
    private InhouseSalesModel $inhouseSalesModel;

    public function __construct()
    {
        $this->inhouseSalesModel = new InhouseSalesModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $db = Database::getInstance();
        $asmStmt = $db->prepare("
            SELECT DISTINCT sm.manager_name
            FROM (
                SELECT
                    TRIM(COALESCE(m.manager_name, '')) AS manager_name
                FROM managers m
                WHERE LOWER(TRIM(COALESCE(m.position, ''))) IN ('asm_manager', 'area_sales_manager')

                UNION ALL

                SELECT
                    TRIM(
                        COALESCE(
                            NULLIF(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')), ' '),
                            u.name,
                            ''
                        )
                    ) AS manager_name
                FROM users u
                WHERE LOWER(TRIM(COALESCE(u.role, ''))) = 'asm_manager'
            ) sm
            WHERE sm.manager_name <> ''
            ORDER BY sm.manager_name ASC
        ");
        $asmStmt->execute();
        $asmManagers = array_map(
            static fn(array $row): string => (string)($row['manager_name'] ?? ''),
            $asmStmt->fetchAll(\PDO::FETCH_ASSOC)
        );

        $stmt = $db->prepare("
            SELECT
                i.row_id AS id,
                i.inhouse_id,
                i.user_type,
                i.first_name,
                i.middle_name,
                i.last_name,
                i.email,
                i.contact,
                i.sales_manager,
                i.sales_category,
                i.password,
                i.photos,
                i.address,
                i.status,
                i.source_type,
                i.created_at
            FROM (
                SELECT
                    ih.id AS row_id,
                    ih.inhouse_id,
                    'inhouse' AS source_type,
                    COALESCE(NULLIF(ih.user_type, ''), 'inhouse_sales') AS user_type,
                    ih.first_name,
                    ih.middle_name,
                    ih.last_name,
                    ih.email,
                    ih.contact,
                    ih.sales_manager,
                    ih.sales_category,
                    ih.password,
                    ih.photos,
                    ih.address,
                    ih.status,
                    ih.created_at
                FROM inhouse_sales ih

                UNION ALL

                SELECT
                    u.id AS row_id,
                    u.id AS inhouse_id,
                    'user' AS source_type,
                    u.role AS user_type,
                    COALESCE(NULLIF(TRIM(u.first_name), ''), TRIM(u.name)) AS first_name,
                    COALESCE(NULLIF(TRIM(u.middle_name), ''), '') AS middle_name,
                    COALESCE(NULLIF(TRIM(u.last_name), ''), '') AS last_name,
                    COALESCE(u.email, '') AS email,
                    COALESCE(u.contact_no, '') AS contact,
                    '' AS sales_manager,
                    '' AS sales_category,
                    u.password,
                    u.avatar AS photos,
                    '' AS address,
                    u.status,
                    u.created_at
                FROM users u
                WHERE u.role = 'inhouse_sales'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM inhouse_sales ix
                      WHERE (ix.user_id IS NOT NULL AND ix.user_id = u.id)
                         OR (ix.email IS NOT NULL AND u.email IS NOT NULL AND ix.email = u.email)
                  )
            ) AS i
            ORDER BY i.created_at DESC, i.row_id DESC
        ");
        $stmt->execute();
        $inhouseUsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('inhouse_sales.inhouse.index', [
            'title' => 'In-House',
            'inhouseUsers' => $inhouseUsers,
            'asmManagers' => $asmManagers,
        ]);
    }


    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $sales_manager = trim($data['sales_manager'] ?? '');
        $sales_category = trim($data['sales_category'] ?? '');
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $contact = trim($data['contact'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim((string)($data['password'] ?? ''));
        $address = trim($data['address'] ?? '');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'sales_manager' => 'required|max:100',
            'sales_category' => 'required|max:100',
            'first_name' => 'required|min:2|max:100',
            'middle_name' => 'max:100',
            'last_name' => 'required|min:2|max:100',
            'contact' => 'required|regex:/^\d{11}$/',
            'email' => 'required|email|unique:inhouse_sales.email',
            'password' => $password !== '' ? 'min:8' : 'max:255',
            'address' => 'max:255',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('sales_manager', 'sales_category', 'first_name', 'middle_name', 'last_name', 'contact', 'email', 'password', 'address', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add in-house user. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $photos = null;
        if (isset($_FILES['photos']) && ($_FILES['photos']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photos = Upload::store($_FILES['photos'], 'inhouse_sales', 'inh_');
            if ($photos === null) {
                Session::flash('message', 'Photo upload failed. Please use a valid image file (max 5MB).');
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $this->inhouseSalesModel->createInhouseSales([
            'user_id' => null,
            'user_type' => 'inhouse_sales',
            'sales_manager' => $sales_manager,
            'first_name' => $first_name,
            'middle_name' => $middle_name ?: null,
            'last_name' => $last_name,
            'contact' => $contact,
            'email' => $email,
            'password' => $password !== '' ? password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) : null,
            'photos' => $photos,
            'address' => $address ?: null,
            'sales_category' => $sales_category,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'inhouse_create', "Created in-house user: {$first_name} {$last_name}");

        Session::flash('message', 'In-house user added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('inhouse'));
    }

    public function update(string $source, int $id): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $sales_manager = trim($data['sales_manager'] ?? '');
        $sales_category = trim($data['sales_category'] ?? '');
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $contact = trim($data['contact'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim((string)($data['password'] ?? ''));
        $address = trim($data['address'] ?? '');
        $status = trim($data['status'] ?? 'active');

        $rules = [
            'sales_manager' => 'required|max:100',
            'sales_category' => 'required|max:100',
            'first_name' => 'required|min:2|max:100',
            'middle_name' => 'max:100',
            'last_name' => 'required|min:2|max:100',
            'contact' => 'required|regex:/^\d{11}$/',
            'email' => 'required|email',
            'password' => $password !== '' ? 'min:8' : 'max:255',
            'address' => 'max:255',
            'status' => 'required|in:pending,active,inactive',
        ];

        $validator = (new Validation())->validate(
            compact('sales_manager', 'sales_category', 'first_name', 'middle_name', 'last_name', 'contact', 'email', 'password', 'address', 'status'),
            $rules
        );

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('message', 'Unable to update in-house user. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $db = Database::getInstance();
        $hashedPassword = $password !== '' ? password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) : null;

        $photos = null;
        if (isset($_FILES['photos']) && ($_FILES['photos']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photos = Upload::store($_FILES['photos'], 'inhouse_sales', 'inh_');
            if ($photos === null) {
                $errors = ['photos' => ['Photo upload failed. Please use a valid image file (max 5MB).']];
                if (App::isAjax() || App::isApiRequest()) {
                    Validation::jsonResponse($errors);
                }
                Session::flash('message', $errors['photos'][0]);
                Session::flash('message_type', 'error');
                $this->redirectBack();
            }
        }

        $updated = false;
        $linkedUserId = 0;
        $now = date('Y-m-d H:i:s');

        if ($source === 'inhouse') {
            $fetch = $db->prepare("SELECT id, user_id, photos FROM inhouse_sales WHERE id = :id LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                $this->respondInhouseUpdateNotFound();
            }

            $linkedUserId = (int)($row['user_id'] ?? 0);
            $payload = [
                ':id' => $id,
                ':sales_manager' => $sales_manager,
                ':sales_category' => $sales_category,
                ':first_name' => $first_name,
                ':middle_name' => $middle_name !== '' ? $middle_name : null,
                ':last_name' => $last_name,
                ':contact' => $contact,
                ':email' => $email,
                ':address' => $address !== '' ? $address : null,
                ':status' => $status,
                ':updated_at' => $now,
            ];
            $setPassword = '';
            if ($hashedPassword !== null) {
                $setPassword = ', password = :password';
                $payload[':password'] = $hashedPassword;
            }
            $setPhotos = '';
            if ($photos !== null) {
                $setPhotos = ', photos = :photos';
                $payload[':photos'] = $photos;
            }

            $stmt = $db->prepare("
                UPDATE inhouse_sales
                SET
                    user_type = 'inhouse_sales',
                    sales_manager = :sales_manager,
                    first_name = :first_name,
                    middle_name = :middle_name,
                    last_name = :last_name,
                    contact = :contact,
                    email = :email,
                    address = :address,
                    sales_category = :sales_category,
                    status = :status,
                    updated_at = :updated_at
                    {$setPassword}
                    {$setPhotos}
                WHERE id = :id
                LIMIT 1
            ");
            $stmt->execute($payload);
            $updated = $stmt->rowCount() > 0;
        } elseif ($source === 'user') {
            $fetch = $db->prepare("SELECT id, password FROM users WHERE id = :id AND role = 'inhouse_sales' LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                $this->respondInhouseUpdateNotFound();
            }

            $linkedUserId = $id;
            $existing = $db->prepare("SELECT id FROM inhouse_sales WHERE user_id = :user_id OR email = :email LIMIT 1");
            $existing->execute([':user_id' => $id, ':email' => $email]);
            $existingRow = $existing->fetch(\PDO::FETCH_ASSOC) ?: null;

            if ($existingRow) {
                $source = 'inhouse';
                $id = (int)$existingRow['id'];
                $payload = [
                    ':id' => $id,
                    ':user_id' => $linkedUserId,
                    ':sales_manager' => $sales_manager,
                    ':sales_category' => $sales_category,
                    ':first_name' => $first_name,
                    ':middle_name' => $middle_name !== '' ? $middle_name : null,
                    ':last_name' => $last_name,
                    ':contact' => $contact,
                    ':email' => $email,
                    ':address' => $address !== '' ? $address : null,
                    ':status' => $status,
                    ':updated_at' => $now,
                ];
                $setPassword = '';
                if ($hashedPassword !== null) {
                    $setPassword = ', password = :password';
                    $payload[':password'] = $hashedPassword;
                }
                $setPhotos = '';
                if ($photos !== null) {
                    $setPhotos = ', photos = :photos';
                    $payload[':photos'] = $photos;
                }

                $stmt = $db->prepare("
                    UPDATE inhouse_sales
                    SET
                        user_id = :user_id,
                        user_type = 'inhouse_sales',
                        sales_manager = :sales_manager,
                        first_name = :first_name,
                        middle_name = :middle_name,
                        last_name = :last_name,
                        contact = :contact,
                        email = :email,
                        address = :address,
                        sales_category = :sales_category,
                        status = :status,
                        updated_at = :updated_at
                        {$setPassword}
                        {$setPhotos}
                    WHERE id = :id
                    LIMIT 1
                ");
                $stmt->execute($payload);
            } else {
                $this->inhouseSalesModel->createInhouseSales([
                    'user_id' => $id,
                    'user_type' => 'inhouse_sales',
                    'sales_manager' => $sales_manager,
                    'first_name' => $first_name,
                    'middle_name' => $middle_name ?: null,
                    'last_name' => $last_name,
                    'contact' => $contact,
                    'email' => $email,
                    'password' => $hashedPassword ?? ($row['password'] ?? null),
                    'photos' => $photos,
                    'address' => $address ?: null,
                    'sales_category' => $sales_category,
                    'status' => $status,
                ]);
            }
        } else {
            $errors = ['source' => ['Invalid in-house source.']];
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($errors, 422);
            }
            Session::flash('message', 'Invalid in-house source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('inhouse'));
        }

        if ($linkedUserId > 0) {
            $this->syncLinkedUserProfile($linkedUserId, [
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'email' => $email,
                'contact' => $contact,
                'status' => $status,
                'password' => $hashedPassword,
                'photos' => $photos,
            ]);
            $updated = true;
        }

        (new ActivityLogModel())->log(Auth::id(), 'inhouse_update', "Updated in-house {$source} ID: {$id}");

        Session::flash('message', 'In-house user updated successfully.');
        Session::flash('message_type', 'success');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json([
                'success' => true,
                'updated' => $updated,
                'redirect' => App::url('inhouse'),
            ]);
        }

        $this->redirect(App::url('inhouse'));
    }

    public function approve(string $source, int $id): void
    {
        Csrf::verify();

        $db = Database::getInstance();
        $updated = false;

        if ($source === 'inhouse') {
            $fetch = $db->prepare("SELECT id, user_id, email, first_name, last_name, status FROM inhouse_sales WHERE id = :id LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                Session::flash('message', 'In-house record not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('inhouse'));
            }

            $stmt = $db->prepare("UPDATE inhouse_sales SET status = 'active', updated_at = :updated_at WHERE id = :id AND status = 'pending'");
            $stmt->execute([
                ':id' => $id,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);
            $updated = $stmt->rowCount() > 0;

            $linkedUserId = (int)($row['user_id'] ?? 0);
            if ($linkedUserId > 0) {
                $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE id = :id AND status = 'pending'")
                    ->execute([':id' => $linkedUserId, ':updated_at' => date('Y-m-d H:i:s')]);
            } elseif (!empty($row['email'])) {
                $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE role = 'inhouse_sales' AND email = :email AND status = 'pending'")
                    ->execute([':email' => $row['email'], ':updated_at' => date('Y-m-d H:i:s')]);
            }
        } elseif ($source === 'user') {
            $fetch = $db->prepare("SELECT id, email, name, status FROM users WHERE id = :id AND role = 'inhouse_sales' LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                Session::flash('message', 'In-house user not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('inhouse'));
            }

            $stmt = $db->prepare("UPDATE users SET status = 'active', updated_at = :updated_at WHERE id = :id AND role = 'inhouse_sales' AND status = 'pending'");
            $stmt->execute([
                ':id' => $id,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);
            $updated = $stmt->rowCount() > 0;

            if (!empty($row['email'])) {
                $db->prepare("UPDATE inhouse_sales SET status = 'active', updated_at = :updated_at WHERE email = :email AND status = 'pending'")
                    ->execute([':email' => $row['email'], ':updated_at' => date('Y-m-d H:i:s')]);
            }
        } else {
            Session::flash('message', 'Invalid in-house source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('inhouse'));
        }

        if ($updated) {
            (new ActivityLogModel())->log(Auth::id(), 'inhouse_approve', "Approved in-house {$source} ID: {$id}");
            Session::flash('message', 'In-house user approved successfully.');
            Session::flash('message_type', 'success');
        } else {
            Session::flash('message', 'In-house user is already approved or inactive.');
            Session::flash('message_type', 'info');
        }

        $this->redirect(App::url('inhouse'));
    }

    public function delete(string $source, int $id): void
    {
        Csrf::verify();

        $db = Database::getInstance();
        $deleted = false;

        if ($source === 'inhouse') {
            $fetch = $db->prepare("SELECT id, user_id, email, first_name, last_name FROM inhouse_sales WHERE id = :id LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                Session::flash('message', 'In-house record not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('inhouse'));
            }

            $del = $db->prepare("DELETE FROM inhouse_sales WHERE id = :id LIMIT 1");
            $del->execute([':id' => $id]);
            $deleted = $del->rowCount() > 0;

            $linkedUserId = (int)($row['user_id'] ?? 0);
            if ($linkedUserId > 0) {
                $db->prepare("DELETE FROM users WHERE id = :id AND role = 'inhouse_sales' LIMIT 1")->execute([':id' => $linkedUserId]);
            }
        } elseif ($source === 'user') {
            $fetch = $db->prepare("SELECT id, email, name FROM users WHERE id = :id AND role = 'inhouse_sales' LIMIT 1");
            $fetch->execute([':id' => $id]);
            $row = $fetch->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                Session::flash('message', 'In-house user not found.');
                Session::flash('message_type', 'error');
                $this->redirect(App::url('inhouse'));
            }

            $delUser = $db->prepare("DELETE FROM users WHERE id = :id AND role = 'inhouse_sales' LIMIT 1");
            $delUser->execute([':id' => $id]);
            $deleted = $delUser->rowCount() > 0;

            if (!empty($row['email'])) {
                $db->prepare("DELETE FROM inhouse_sales WHERE email = :email")->execute([':email' => $row['email']]);
            }
        } else {
            Session::flash('message', 'Invalid in-house source.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('inhouse'));
        }

        if ($deleted) {
            (new ActivityLogModel())->log(Auth::id(), 'inhouse_delete', "Deleted in-house {$source} ID: {$id}");
            Session::flash('message', 'In-house user deleted successfully.');
            Session::flash('message_type', 'success');
        } else {
            Session::flash('message', 'No in-house record was deleted.');
            Session::flash('message_type', 'info');
        }

        $this->redirect(App::url('inhouse'));
    }

    private function syncLinkedUserProfile(int $userId, array $data): void
    {
        $db = Database::getInstance();
        $first = trim((string)($data['first_name'] ?? ''));
        $middle = trim((string)($data['middle_name'] ?? ''));
        $last = trim((string)($data['last_name'] ?? ''));
        $name = trim(preg_replace('/\s+/', ' ', trim("$first $middle $last")) ?? '');

        $payload = [
            ':id' => $userId,
            ':name' => $name,
            ':first_name' => $first,
            ':middle_name' => $middle !== '' ? $middle : null,
            ':last_name' => $last,
            ':email' => trim((string)($data['email'] ?? '')),
            ':contact_no' => trim((string)($data['contact'] ?? '')),
            ':status' => trim((string)($data['status'] ?? 'active')),
            ':updated_at' => date('Y-m-d H:i:s'),
        ];

        $setPassword = '';
        if (!empty($data['password'])) {
            $setPassword = ', password = :password';
            $payload[':password'] = $data['password'];
        }

        $setAvatar = '';
        if (!empty($data['photos'])) {
            $setAvatar = ', avatar = :avatar';
            $payload[':avatar'] = $data['photos'];
        }

        $stmt = $db->prepare("
            UPDATE users
            SET
                name = :name,
                first_name = :first_name,
                middle_name = :middle_name,
                last_name = :last_name,
                email = :email,
                contact_no = :contact_no,
                status = :status,
                updated_at = :updated_at
                {$setPassword}
                {$setAvatar}
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute($payload);
    }

    private function respondInhouseUpdateNotFound(): void
    {
        if (App::isAjax() || App::isApiRequest()) {
            Validation::jsonResponse(['id' => ['In-house user not found.']], 404);
        }

        Session::flash('message', 'In-house user not found.');
        Session::flash('message_type', 'error');
        $this->redirect(App::url('inhouse'));
    }
}
