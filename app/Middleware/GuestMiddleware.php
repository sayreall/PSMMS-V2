<?php

namespace App\Middleware;

use App\Helpers\Auth;
use App\Config\App;

class GuestMiddleware
{
    public function handle(): ?string
    {
        Auth::init();

        if (Auth::check()) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }

        return null;
    }
}