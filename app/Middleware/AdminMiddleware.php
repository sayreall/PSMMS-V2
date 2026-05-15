<?php

namespace App\Middleware;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Validation;

class AdminMiddleware
{
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

        if (!Auth::hasRole('admin')) {
            if (App::isAjax() || App::isApiRequest()) {
                Validation::jsonResponse(['auth' => ['Forbidden.']], 403);
            }
            include_once App::basePath('app/Views/auth/errors/403.php');
            exit;
        }

        return null;
    }
}
