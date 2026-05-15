<?php

namespace App\Config;

class Route
{
    public string $method;
    public string $pattern;
    public $handler;
    public array $middleware;

    public function __construct(string $method, string $pattern, $handler, array $middleware = [])
    {
        $this->method = strtoupper($method);
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->middleware = $middleware;
    }
}

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $middlewareStack = [];
    private ?string $groupPrefix = null;
    private array $groupMiddleware = [];

    public function get(string $pattern, $handler, array $middleware = []): self
    {
        return $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, $handler, array $middleware = []): self
    {
        return $this->add('POST', $pattern, $handler, $middleware);
    }

    public function put(string $pattern, $handler, array $middleware = []): self
    {
        return $this->add('PUT', $pattern, $handler, $middleware);
    }

    public function delete(string $pattern, $handler, array $middleware = []): self
    {
        return $this->add('DELETE', $pattern, $handler, $middleware);
    }

    public function any(string $pattern, $handler, array $middleware = []): self
    {
        return $this->add('*', $pattern, $handler, $middleware);
    }

    private function add(string $method, string $pattern, $handler, array $middleware = []): self
    {
        if ($this->groupPrefix) {
            $pattern = $this->groupPrefix . $pattern;
        }
        $middleware = array_merge($this->groupMiddleware, $middleware);

        $route = new Route($method, $pattern, $handler, $middleware);
        $this->routes[] = $route;

        return $this;
    }

    public function name(string $name): self
    {
        $lastRoute = end($this->routes);
        if ($lastRoute) {
            $this->namedRoutes[$name] = $lastRoute->pattern;
        }
        return $this;
    }

    public function group(string $prefix, callable $callback, array $middleware = []): self
    {
        $oldPrefix = $this->groupPrefix;
        $oldMiddleware = $this->groupMiddleware;

        $this->groupPrefix = ($this->groupPrefix ?? '') . $prefix;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);

        $callback($this);

        $this->groupPrefix = $oldPrefix;
        $this->groupMiddleware = $oldMiddleware;

        return $this;
    }

    public function dispatch(string $requestUri, string $requestMethod): mixed
    {
        $requestUri = $this->normalizeUri($requestUri);
        $requestMethod = strtoupper($requestMethod);

        // Handle PUT/DELETE/PATCH from form method override
        if ($requestMethod === 'POST') {
            $overrideMethod = $_POST['_method'] ?? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null;
            if ($overrideMethod) {
                $requestMethod = strtoupper($overrideMethod);
            }
        }

        foreach ($this->routes as $route) {
            if ($route->method !== '*' && $route->method !== $requestMethod) {
                continue;
            }

            $pattern = '#^' . $this->convertPatternToRegex($route->pattern) . '$#i';

            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches);

                $params = $this->extractNamedParams($route->pattern, $matches);

                // Execute middleware
                foreach ($route->middleware as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $result = $middleware->handle();
                    if ($result !== null) {
                        return $result;
                    }
                }

                return $this->callHandler($route->handler, $params);
            }
        }

        http_response_code(404);
        return $this->handle404();
    }

    private function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

        // When hosted in a subdirectory (e.g. /PSMMS-V2/public),
        // strip the front-controller directory so routes still match.
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($basePath !== '' && $basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        return rtrim($uri, '/') ?: '/';
    }

    private function convertPatternToRegex(string $pattern): string
    {
        $pattern = trim($pattern, '/');
        $segments = explode('/', $pattern);
        $regex = '';

        foreach ($segments as $segment) {
            if (preg_match('/^{(\w+)}$/', $segment, $matches)) {
                $regex .= '/([^/]+)';
            } elseif (preg_match('/^{(\w+)\?}$/', $segment, $matches)) {
                $regex .= '(?:/([^/]+))?';
            } else {
                $regex .= '/' . preg_quote($segment, '#');
            }
        }

        return $regex ?: '\/';
    }

    private function extractNamedParams(string $pattern, array $matches): array
    {
        $params = [];
        preg_match_all('/\{(\w+)\??\}/', $pattern, $names);

        foreach ($names[1] as $index => $name) {
            $params[$name] = $matches[$index] ?? null;
        }

        return $params;
    }

    private function callHandler($handler, array $params): mixed
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);
            $class = "App\\Controllers\\$class";

            if (class_exists($class)) {
                $controller = new $class();
                return call_user_func_array([$controller, $method], $params);
            }
        }

        throw new \RuntimeException("Route handler not callable: " . (is_string($handler) ? $handler : gettype($handler)));
    }

    private function handle404(): void
    {
        if (App::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not Found', 'code' => 404]);
            return;
        }
        include_once App::basePath('app/Views/auth/errors/404.php');
    }

    public function getNamedRoute(string $name): ?string
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
