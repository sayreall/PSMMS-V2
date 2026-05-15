<?php

/**
 * Application Bootstrap
 * Initializes all core components
 */

define('ROOT_PATH', dirname(__DIR__, 1));

require_once ROOT_PATH . '/vendor/autoload.php';

// Initialize core configs
\App\Config\App::init();
\App\Config\Database::loadEnv();
\App\Helpers\Auth::init();
\App\Helpers\Session::init();
\App\Helpers\Csrf::init();