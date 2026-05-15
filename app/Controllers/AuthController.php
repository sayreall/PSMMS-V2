<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Validation;
use App\Models\ManagerModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    private UserModel $userModel;
    private ManagerModel $managerModel;
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_LOCKOUT_SECONDS = 300;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->managerModel = new ManagerModel();
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

            // Regenerate session for security
            Session::regenerate();

            // Log activity
            (new \App\Models\ActivityLogModel())->log($user['id'], 'login', 'User logged in');

            // Handle JWT for API
            if (App::isApiRequest()) {
                $token = Auth::attemptWithJWT(['email' => $email, 'password' => $password]);
                $this->json(['token' => $token, 'user' => $user]);
            }

            $redirect = $this->normalizeRedirectUrl(Session::getFlash('redirect'));

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
        return $this->render('auth.register', [
            'title' => 'Register',
            'subtitle' => 'Create your account in minutes.',
            'errors' => Session::getFlash('errors'),
            'old' => Session::getFlash('old'),
        ]);
    }

    public function register(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $first_name = trim($data['first_name'] ?? '');
        $middle_name = trim($data['middle_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $name = $this->composeFullName($first_name, $middle_name, $last_name);
        $email = trim($data['email'] ?? '');
        $company_email = trim($data['company_email'] ?? '');
        $contact_no = trim($data['contact_no'] ?? '');
        $password = $data['password'] ?? '';
        $password_confirmation = $data['password_confirmation'] ?? '';
        $role = trim($data['role'] ?? 'accounting');

        $rules = [
            'first_name'            => 'required|min:2|max:100',
            'middle_name'           => 'max:100',
            'last_name'             => 'required|min:2|max:100',
            'email'                 => 'required|email|unique:users.email',
            'company_email'         => 'required|email|unique:users.company_email',
            'contact_no'            => 'required|min:7|max:30',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation'  => 'required',
            'role'                  => 'required|in:accounting,asm_manager,head_manager',
        ];

        $validator = (new Validation)->validate(
            compact('first_name', 'middle_name', 'last_name', 'email', 'company_email', 'contact_no', 'password', 'password_confirmation', 'role'),
            $rules
        );

        if (!$validator->passes()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse($validator->errors());
            }
            Session::flash('errors', $validator->errors());
            Session::flash('old', compact('first_name', 'middle_name', 'last_name', 'email', 'company_email', 'contact_no', 'role'));
            $this->redirectBack();
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

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
                    'position' => $role,
                    'contact_no' => $contact_no,
                    'company_email' => $company_email,
                    'email' => $email,
                    'profile_picture' => null,
                    'status' => 'pending',
                ]);
            }
        }

        // Log activity
        (new \App\Models\ActivityLogModel())->log($userId, 'register', 'New user registered');

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

    public function logout(): never
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

    private function normalizeRedirectUrl(?string $redirect): string
    {
        if (!$redirect) {
            return App::url('login.php');
        }

        // Keep absolute URLs as-is.
        if (preg_match('#^https?://#i', $redirect)) {
            return $redirect;
        }

        $path = parse_url($redirect, PHP_URL_PATH) ?? '';
        $query = parse_url($redirect, PHP_URL_QUERY);
        $appPath = parse_url(App::$url, PHP_URL_PATH) ?? '';

        if ($appPath && str_starts_with($path, $appPath)) {
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
}
