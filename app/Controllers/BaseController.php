<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Session;
use App\Helpers\Auth;
use App\Helpers\Validation;

class BaseController
{
    protected string $layout = 'main';
    protected array $middleware = [];

    protected function render(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);

        $flash = [
            'message' => Session::getFlash('message'),
            'type'    => Session::getFlash('message_type') ?? 'success',
        ];

        $user = Auth::user();
        $csrfToken = Session::csrfToken();

        $viewPath = $this->resolveViewPath($view);

        if (App::isAjax()) {
            ob_start();
            include_once $viewPath;
            return ob_get_clean();
        }

        ob_start();
        include_once $viewPath;
        $content = ob_get_clean();

        $layout = $this->layout;
        if (strpos($layout, 'layouts.') === 0) {
            $layout = substr($layout, strlen('layouts.'));
        }

        ob_start();
        include_once App::basePath("app/Views/layouts/{$layout}.php");
        return ob_get_clean();
    }

    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): never
    {
        header("Location: $url");
        exit;
    }

    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? App::url('dashboard');
        header("Location: $referer");
        exit;
    }

    protected function flash(string $message, string $type = 'success'): void
    {
        Session::flash('message', $message);
        Session::flash('message_type', $type);
    }

    protected function validate(array $data, array $rules): array
    {
        $validator = (new Validation)->validate($data, $rules);
        if ($validator->fails()) {
            Validation::jsonResponse($validator->errors());
        }
        return $data;
    }

    protected function requestData(): array
    {
        $data = $_POST;

        if (App::isApiRequest()) {
            $raw = file_get_contents('php://input');
            if (!empty($raw)) {
                $json = json_decode($raw, true);
                if (is_array($json)) {
                    $data = array_merge($data, $json);
                }
            }
        }

        return $data;
    }

    private function resolveViewPath(string $view): string
    {
        $path = str_replace('.', '/', $view);
        $fullPath = App::basePath("app/Views/$path.php");

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("View not found: $fullPath");
        }

        return $fullPath;
    }
}
