<?php

namespace App\Controllers;

use App\Config\App;
use App\Config\Database;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\DashboardRouteResolver;
use App\Models\AdminModel;
use App\Models\InhouseSalesModel;
use App\Models\ManagerModel;
use App\Models\MsaPartnerModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    private UserModel $userModel;
    private ManagerModel $managerModel;
    private AdminModel $adminModel;
    private InhouseSalesModel $inhouseSalesModel;
    private MsaPartnerModel $msaPartnerModel;
    private DashboardRouteResolver $dashboardRouteResolver;
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_LOCKOUT_SECONDS = 300;
    private const INHOUSE_PRODUCT_OPTIONS = ['surf2sawa', 'fiberx', 'bida', 'sme'];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->managerModel = new ManagerModel();
        $this->adminModel = new AdminModel();
        $this->inhouseSalesModel = new InhouseSalesModel();
        $this->msaPartnerModel = new MsaPartnerModel();
        $this->dashboardRouteResolver = new DashboardRouteResolver();
        $this->layout = 'layouts.auth';
    }

    public function showLogin(): string
    {
        return $this->render('auth.login', [
            'title' => 'Login',
            'subtitle' => 'Welcome back. Sign in to continue.',
            'errors' => Session::getFlash('errors'),
            'old' => Session::getFlash('old'),
        ]);
    }

    public function login(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $remember = !empty($data['remember']);
        $throttleKey = $this->throttleKey($email);

        if ($this->isLoginThrottled($throttleKey)) {
            $waitSeconds = max(1, (int) (Session::get($throttleKey)['locked_until'] - time()));
            $errors = ['email' => ["Too many login attempts. Try again in {$waitSeconds} seconds."]];
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($errors, 429);
            }
            Session::flash('errors', $errors);
            Session::flash('old', ['email' => $email]);
            $this->redirectBack();
        }

        $rules = [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ];

        $validator = (new Validation)->validate(
            ['email' => $email, 'password' => $password],
            $rules
        );

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('errors', $validator->errors());
            Session::flash('old', ['email' => $email]);
            $this->redirectBack();
        }

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            Session::remove($throttleKey);
            $user = Auth::user();
            if (!is_array($user) || !isset($user['id'])) {
                $errors = ['email' => ['Unable to load authenticated user.']];
                if (App::isAjax() || App::isApiRequest()) {
                    Validation::jsonResponse($errors, 500);
                }
                Session::flash('errors', $errors);
                Session::flash('old', ['email' => $email]);
                $this->redirectBack();
            }

            // Regenerate session for security
            Session::regenerate();

            // Log activity
            (new \App\Models\ActivityLogModel())->log($user['id'], 'login', 'User logged in');

            // Handle JWT for API
            if (App::isApiRequest()) {
                $token = Auth::attemptWithJWT(['email' => $email, 'password' => $password]);
                $this->json(['token' => $token, 'user' => $user]);
            }

            $redirect = $this->normalizeRedirectUrl(Session::getFlash('redirect'), $user);

            if (App::isAjax()) {
                $this->json(['redirect' => $redirect, 'success' => true]);
            }

            $this->redirect($redirect);
        }

        $errors = ['email' => ['Invalid credentials.']];
        $this->recordFailedLogin($throttleKey);

        if (App::isAjax() || App::isApiRequest()) {
            Validation::jsonResponse($errors);
        }

        Session::flash('errors', $errors);
        Session::flash('old', ['email' => $email]);
        $this->redirectBack();
    }

    public function showRegister(): string
    {
        $asmManagers = $this->getInhouseSalesManagerOptions();

        return $this->render('auth.register', [
            'title' => 'Register',
            'subtitle' => 'Create your account in minutes.',
            'errors' => Session::getFlash('errors'),
            'old' => Session::getFlash('old'),
            'asmManagers' => $asmManagers,
            'inhouseProductOptions' => self::INHOUSE_PRODUCT_OPTIONS,
        ]);
    }

    public function register(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $company_email = trim($data['company_email'] ?? '');
        $contact_no = trim($data['contact_no'] ?? '');
        $manager_position = trim($data['manager_position'] ?? '');
        $admin_position = trim($data['admin_position'] ?? '');
        $admin_area = trim($data['admin_area'] ?? '');
        $admin_employee_id = trim($data['admin_employee_id'] ?? '');
        $admin_department = trim($data['admin_department'] ?? '');
        $inhouse_sales_manager = trim($data['inhouse_sales_manager'] ?? '');
        $inhouse_sales_category = trim($data['inhouse_sales_category'] ?? '');
        $inhouse_first_name = trim($data['inhouse_first_name'] ?? '');
        $inhouse_last_name = trim($data['inhouse_last_name'] ?? '');
        $inhouse_employee_id = trim($data['inhouse_employee_id'] ?? '');
        $inhouse_contact = trim($data['inhouse_contact'] ?? '');
        $inhouse_email = trim($data['inhouse_email'] ?? '');
        $msa_company_name = trim($data['msa_company_name'] ?? '');
        $msa_username = trim($data['msa_username'] ?? '');
        $msa_contact = trim($data['msa_contact'] ?? '');
        $msa_address = trim($data['msa_address'] ?? '');
        $msa_installer = trim($data['msa_installer'] ?? '');
        $msa_type = trim($data['msa_type'] ?? '');
        $msa_email = trim($data['msa_email'] ?? '');
        $password = $data['password'] ?? '';
        $password_confirmation = $data['password_confirmation'] ?? '';
        $role = trim($data['role'] ?? 'accounting');

        if ($role === 'inhouse_sales') {
            $first_name = $inhouse_first_name;
            $middle_name = '';
            $last_name = $inhouse_last_name;
            $email = $inhouse_email;
            $contact_no = $inhouse_contact;
            $company_email = '';
        }

        if ($role === 'msa_partners') {
            $first_name = '';
            $middle_name = '';
            $last_name = '';
            $email = $msa_email;
            $contact_no = $msa_contact;
            $company_email = '';
        }

        $name = $this->composeFullName($first_name, $middle_name, $last_name);
        if ($role === 'msa_partners' && $msa_company_name !== '') {
            $name = $msa_company_name;
        }

        if ($role === 'asm_manager' && $company_email === '') {
            $company_email = $email;
        }

        $rules = [
            'first_name'            => 'required|min:2|max:100',
            'middle_name'           => 'max:100',
            'last_name'             => 'required|min:2|max:100',
            'email'                 => 'required|email|unique:users.email',
            'company_email'         => 'required|email|unique:users.company_email',
            'contact_no'            => 'required|regex:/^\d{11}$/',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation'  => 'required',
            'role'                  => 'required|in:accounting,asm_manager,head_manager,super_admin,msa_partners,inhouse_sales,sme_sales',
        ];

        if ($role === 'asm_manager') {
            $rules['manager_position'] = 'required|in:super_manager,area_sales_manager,head_manager';
            $rules['company_email'] = 'email|unique:users.company_email';
        }

        if ($role === 'super_admin') {
            $rules['admin_position'] = 'required';
            $rules['admin_employee_id'] = 'required|regex:/^PCC\d{4}$/|unique:admins.employee_id';
            $rules['admin_department'] = 'required|in:accounting,operation';
        }

        if ($role === 'inhouse_sales') {
            $rules['company_email'] = 'email|unique:users.company_email';
            $rules['inhouse_sales_manager'] = 'required';
            $rules['inhouse_sales_category'] = 'required|in:surf2sawa,fiberx,bida,sme';
            $rules['inhouse_first_name'] = 'required|min:2|max:100';
            $rules['inhouse_last_name'] = 'required|min:2|max:100';
            $rules['inhouse_employee_id'] = 'required|regex:/^PCC\d{4}$/|unique:inhouse_sales.employee_id';
            $rules['inhouse_contact'] = 'required|regex:/^\d{11}$/';
            $rules['inhouse_email'] = 'required|email|unique:inhouse_sales.email';
        }

        if ($role === 'msa_partners') {
            $rules['first_name'] = 'max:100';
            $rules['middle_name'] = 'max:100';
            $rules['last_name'] = 'max:100';
            $rules['company_email'] = 'email|unique:users.company_email';
            $rules['msa_company_name'] = 'required|min:2|max:150';
            $rules['msa_username'] = 'required|min:2|max:100|unique:msa_partners.username';
            $rules['msa_contact'] = 'required|regex:/^\d{11}$/';
            $rules['msa_address'] = 'required|min:5|max:255';
            $rules['msa_installer'] = 'required|min:2|max:150';
            $rules['msa_type'] = 'required|in:regional,ncr';
            $rules['msa_email'] = 'required|email|unique:msa_partners.email';
        }

        $validator = (new Validation)->validate(
            compact(
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'company_email',
                'contact_no',
                'password',
                'password_confirmation',
                'role',
                'manager_position',
                'admin_position',
                'admin_area',
                'admin_employee_id',
                'admin_department',
                'inhouse_sales_manager',
                'inhouse_sales_category',
                'inhouse_first_name',
                'inhouse_last_name',
                'inhouse_employee_id',
                'inhouse_contact',
                'inhouse_email',
                'msa_company_name',
                'msa_username',
                'msa_contact',
                'msa_address',
                'msa_installer',
                'msa_type',
                'msa_email'
            ),
            $rules
        );

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('errors', $validator->errors());
            Session::flash('old', compact(
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'company_email',
                'contact_no',
                'role',
                'manager_position',
                'admin_position',
                'admin_area',
                'admin_employee_id',
                'admin_department',
                'inhouse_sales_manager',
                'inhouse_sales_category',
                'inhouse_first_name',
                'inhouse_last_name',
                'inhouse_employee_id',
                'inhouse_contact',
                'inhouse_email',
                'msa_company_name',
                'msa_username',
                'msa_contact',
                'msa_address',
                'msa_installer',
                'msa_type',
                'msa_email'
            ));
            $this->redirectBack();
        }

        if ($role === 'inhouse_sales') {
            $asmManagers = $this->getInhouseSalesManagerOptions();
            if (!in_array($inhouse_sales_manager, $asmManagers, true)) {
                $errors = ['inhouse_sales_manager' => ['Please select an available ASM Manager.']];
                if (App::isAjax() || App::isApiRequest()) {
                    Validation::jsonResponse($errors);
                }
                Session::flash('errors', $errors);
                Session::flash('old', compact(
                    'first_name', 'middle_name', 'last_name', 'email', 'company_email', 'contact_no', 'role',
                    'manager_position', 'admin_position', 'admin_area', 'admin_employee_id', 'admin_department',
                    'inhouse_sales_manager', 'inhouse_sales_category', 'inhouse_first_name', 'inhouse_last_name',
                    'inhouse_employee_id', 'inhouse_contact', 'inhouse_email', 'msa_company_name', 'msa_username',
                    'msa_contact', 'msa_address', 'msa_installer', 'msa_type', 'msa_email'
                ));
                $this->redirectBack();
            }
        }

        $profileErrors = [];
        $adminProfilePicture = null;
        $inhouseProfilePicture = null;
        $msaProfilePicture = null;

        if ($role === 'super_admin' && isset($_FILES['profile_picture']) && ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $adminProfilePicture = Upload::store($_FILES['profile_picture'], 'admins', 'adm_');
            if (!$adminProfilePicture) {
                $profileErrors['profile_picture'][] = 'Profile picture upload failed. Please use a valid image file (max 5MB).';
            }
        }

        if ($role === 'inhouse_sales' && isset($_FILES['inhouse_profile_picture']) && ($_FILES['inhouse_profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $inhouseProfilePicture = Upload::store($_FILES['inhouse_profile_picture'], 'inhouse_sales', 'inh_');
            if (!$inhouseProfilePicture) {
                $profileErrors['inhouse_profile_picture'][] = 'Profile picture upload failed. Please use a valid image file (max 5MB).';
            }
        }

        if ($role === 'msa_partners' && isset($_FILES['msa_profile_picture']) && ($_FILES['msa_profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $msaProfilePicture = Upload::store($_FILES['msa_profile_picture'], 'msa_partners', 'msa_');
            if (!$msaProfilePicture) {
                $profileErrors['msa_profile_picture'][] = 'Profile picture upload failed. Please use a valid image file (max 5MB).';
            }
        }

        if (!empty($profileErrors)) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($profileErrors);
            }
            Session::flash('errors', $profileErrors);
            Session::flash('old', compact(
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'company_email',
                'contact_no',
                'role',
                'manager_position',
                'admin_position',
                'admin_area',
                'admin_employee_id',
                'admin_department',
                'inhouse_sales_manager',
                'inhouse_sales_category',
                'inhouse_first_name',
                'inhouse_last_name',
                'inhouse_employee_id',
                'inhouse_contact',
                'inhouse_email',
                'msa_company_name',
                'msa_username',
                'msa_contact',
                'msa_address',
                'msa_installer',
                'msa_type',
                'msa_email'
            ));
            $this->redirectBack();
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $company_email = $company_email !== '' ? $company_email : null;
        $contact_no = $contact_no !== '' ? $contact_no : null;

        $userId = 0;
        try {
            Database::beginTransaction();

            $userId = $this->userModel->createUser([
                'name'     => $name,
                'first_name' => $first_name,
                'middle_name' => $middle_name ?: null,
                'last_name' => $last_name,
                'email'    => $email,
                'company_email' => $company_email,
                'contact_no' => $contact_no,
                'password' => $hashedPassword,
                'role'     => $role,
                'status'   => 'pending',
            ]);

            if ($role === 'asm_manager') {
                $exists = $this->managerModel->findBy('email', $email);
                if (!$exists) {
                    $this->managerModel->createManager([
                        'user_id' => $userId,
                        'manager_name' => $name,
                        'position' => $manager_position ?: $role,
                        'contact_no' => $contact_no,
                        'company_email' => $company_email,
                        'email' => $email,
                        'profile_picture' => null,
                        'status' => 'pending',
                    ]);
                }
            }

            if ($role === 'super_admin') {
                $this->adminModel->createAdmin([
                    'user_id' => $userId,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'position' => $admin_position,
                    'area' => $admin_area ?: null,
                    'contact_no' => $contact_no,
                    'employee_id' => $admin_employee_id,
                    'department' => $admin_department,
                    'company_email' => $company_email,
                    'email' => $email,
                    'profile_picture' => $adminProfilePicture,
                    'status' => 'pending',
                ]);
            }

            if ($role === 'inhouse_sales') {
                $this->inhouseSalesModel->createInhouseSales([
                    'user_id' => $userId,
                    'sales_manager' => $inhouse_sales_manager,
                    'sales_category' => $inhouse_sales_category,
                    'first_name' => $inhouse_first_name,
                    'last_name' => $inhouse_last_name,
                    'employee_id' => $inhouse_employee_id,
                    'contact_no' => $inhouse_contact,
                    'email' => $inhouse_email,
                    'profile_picture' => $inhouseProfilePicture,
                    'status' => 'pending',
                ]);
            }

            if ($role === 'msa_partners') {
                $this->msaPartnerModel->createMsaPartner([
                    'user_id' => $userId,
                    'company_name' => $msa_company_name,
                    'username' => $msa_username,
                    'contact_no' => $msa_contact,
                    'address' => $msa_address,
                    'installer' => $msa_installer,
                    'msa_type' => $msa_type,
                    'email' => $msa_email,
                    'profile_picture' => $msaProfilePicture,
                    'status' => 'pending',
                ]);
            }

            Database::commit();
        } catch (\Throwable $e) {
            $db = Database::getInstance();
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log('Registration failed: ' . $e->getMessage());

            if (App::isAjax() || App::isApiRequest()) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => App::$debug ? $e->getMessage() : 'Registration failed. Please try again.',
                    'success' => false,
                ]);
                exit;
            }

            Session::flash('errors', ['form' => ['Registration failed. Please try again.']]);
            $this->redirectBack();
        }

        // Log activity
        if ($userId > 0) {
            (new \App\Models\ActivityLogModel())->log($userId, 'register', 'New user registered');
        }

        Session::flash('message', 'Registration submitted. Head Admin will review and activate your account.');
        Session::flash('message_type', 'info');

        if (App::isAjax() || App::isApiRequest()) {
            $this->json([
                'success' => true,
                'redirect' => App::url('login'),
                'message' => 'Registration submitted. Super Admin will review and activate your account.',
            ]);
        }

        $this->redirect(App::url('login'));
    }

    public function logout(): void
    {
        Csrf::verify();

        $user = Auth::user();
        Auth::logout();

        if ($user) {
            (new \App\Models\ActivityLogModel())->log($user['id'], 'logout', 'User logged out');
        }

        $this->redirect(App::url('login'));
    }

    public function showForgotPassword(): string
    {
        return $this->render('auth.forgot-password', [
            'title' => 'Forgot Password',
            'subtitle' => 'Enter your email to receive a one-time verification code.',
            'errors' => Session::getFlash('errors'),
            'old' => Session::getFlash('old'),
        ]);
    }

    public function forgotPassword(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $email = trim($data['email'] ?? '');

        $validator = (new Validation)->validate(
            ['email' => $email],
            ['email' => 'required|email|exists:users.email']
        );

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('errors', $validator->errors());
            $this->redirectBack();
        }

        $otp = (string) random_int(100000, 999999);
        Session::set('password_reset_email', $email);
        Session::set('password_reset_otp', $otp);
        Session::set('password_reset_otp_expires_at', time() + 600);
        Session::set('password_reset_verified', false);

        // In production replace this with an actual email sender.
        Session::flash('message', 'Verification code generated. Demo OTP: ' . $otp);
        Session::flash('message_type', 'info');
        $this->redirect(App::url('verify-otp'));
    }

    public function showVerifyOtp(): string
    {
        if (!Session::get('password_reset_email')) {
            $this->redirect(App::url('forgot-password'));
        }

        return $this->render('auth.verify-otp', [
            'title' => 'Verify Code',
            'subtitle' => 'Enter the 6-digit code sent to your email.',
            'errors' => Session::getFlash('errors'),
            'old' => Session::getFlash('old'),
            'email' => Session::get('password_reset_email'),
        ]);
    }

    public function verifyOtp(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $otp = trim((string)($data['otp'] ?? ''));

        $rules = [
            'otp' => 'required|min:6|max:6',
        ];

        $validator = (new Validation)->validate(['otp' => $otp], $rules);
        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('errors', $validator->errors());
            Session::flash('old', ['otp' => $otp]);
            $this->redirectBack();
        }

        $expectedOtp = (string) Session::get('password_reset_otp', '');
        $expiresAt = (int) Session::get('password_reset_otp_expires_at', 0);

        if ($expectedOtp === '' || time() > $expiresAt || !hash_equals($expectedOtp, $otp)) {
            $errors = ['otp' => ['Invalid or expired verification code.']];
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($errors);
            }
            Session::flash('errors', $errors);
            Session::flash('old', ['otp' => $otp]);
            $this->redirectBack();
        }

        Session::set('password_reset_verified', true);
        Session::remove('password_reset_otp');
        Session::remove('password_reset_otp_expires_at');

        if (App::isAjax()) {
            $this->json(['redirect' => App::url('reset-password'), 'success' => true]);
        }

        $this->redirect(App::url('reset-password'));
    }

    public function showResetPassword(): string
    {
        if (!Session::get('password_reset_verified') || !Session::get('password_reset_email')) {
            $this->redirect(App::url('forgot-password'));
        }

        return $this->render('auth.reset-password', [
            'title' => 'Reset Password',
            'subtitle' => 'Create a new secure password for your account.',
            'errors' => Session::getFlash('errors'),
        ]);
    }

    public function resetPassword(): void
    {
        Csrf::verify();

        if (!Session::get('password_reset_verified') || !Session::get('password_reset_email')) {
            $this->redirect(App::url('forgot-password'));
        }

        $data = $this->requestData();
        $password = $data['password'] ?? '';
        $passwordConfirmation = $data['password_confirmation'] ?? '';

        $validator = (new Validation)->validate([
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('errors', $validator->errors());
            $this->redirectBack();
        }

        $email = (string) Session::get('password_reset_email');
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            Session::flash('message', 'Account not found.');
            Session::flash('message_type', 'error');
            $this->redirect(App::url('forgot-password'));
        }

        $this->userModel->updateUser((int)$user['id'], [
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);

        Session::remove('password_reset_email');
        Session::remove('password_reset_verified');

        Session::flash('message', 'Password updated successfully. You can now sign in.');
        Session::flash('message_type', 'success');

        if (App::isAjax()) {
            $this->json(['redirect' => App::url('login'), 'success' => true]);
        }

        $this->redirect(App::url('login'));
    }

    private function throttleKey(string $email): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $normalized = strtolower($email);
        return 'login_throttle_' . hash('sha256', $normalized . '|' . $ip);
    }

    private function isLoginThrottled(string $key): bool
    {
        $state = Session::get($key);
        if (!is_array($state)) {
            return false;
        }

        $lockedUntil = (int) ($state['locked_until'] ?? 0);
        if ($lockedUntil <= time()) {
            Session::remove($key);
            return false;
        }

        return true;
    }

    private function recordFailedLogin(string $key): void
    {
        $state = Session::get($key, ['attempts' => 0, 'locked_until' => 0]);
        $attempts = (int) ($state['attempts'] ?? 0) + 1;
        $lockedUntil = 0;

        if ($attempts >= self::LOGIN_MAX_ATTEMPTS) {
            $lockedUntil = time() + self::LOGIN_LOCKOUT_SECONDS;
            $attempts = 0;
        }

        Session::set($key, [
            'attempts' => $attempts,
            'locked_until' => $lockedUntil,
        ]);
    }

    private function normalizeRedirectUrl(?string $redirect, array $user): string
    {
        if (!$redirect) {
            return $this->dashboardRouteResolver->resolveUrl($user);
        }

        // Keep absolute URLs as-is.
        if (preg_match('#^https?://#i', $redirect)) {
            return $redirect;
        }

        $path = parse_url($redirect, PHP_URL_PATH) ?? '';
        $query = parse_url($redirect, PHP_URL_QUERY);
        $appPath = parse_url(App::$url, PHP_URL_PATH) ?? '';

        if ($appPath && strpos($path, $appPath) === 0) {
            $path = substr($path, strlen($appPath));
        }

        $normalized = App::url(ltrim($path, '/'));
        if ($query) {
            $normalized .= '?' . $query;
        }

        return $normalized;
    }

    private function composeFullName(string $first, string $middle, string $last): string
    {
        return trim(preg_replace('/\s+/', ' ', trim("$first $middle $last")) ?? '');
    }

    private function getInhouseSalesManagerOptions(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT DISTINCT sm.manager_name
            FROM (
                SELECT TRIM(COALESCE(m.manager_name, '')) AS manager_name
                FROM managers m
                WHERE LOWER(TRIM(COALESCE(m.position, ''))) IN ('asm_manager', 'area_sales_manager')

                UNION ALL

                SELECT TRIM(
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
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_values(array_filter(array_map(
            static fn(array $row): string => trim((string)($row['manager_name'] ?? '')),
            $rows
        )));
    }
}
