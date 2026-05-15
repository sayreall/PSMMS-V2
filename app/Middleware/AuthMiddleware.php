<?php

namespace App\Middleware;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Validation;

class AuthMiddleware
{
    private ?string $role;

    public function __construct(string $role = null)
    {
        $this->role = $role;
    }

    public function handle(): ?string
    {
        Auth::init();

        if (!Auth::check()) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse(['auth' => ['Unauthenticated.']], 401);
            }
            Session::flash('redirect', $_SERVER['REQUEST_URI']);
            header('Location: ' . App::url('login'));
            exit;
        }

        if ($this->role && !Auth::hasRole($this->role)) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse(['auth' => ['Forbidden.']], 403);
            }
            include_once App::basePath('app/Views/auth/errors/403.php');
            exit;
        }

        return null;
    }
}