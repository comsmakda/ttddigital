<?php

namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : $path;
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Buang BASE_PATH (kalau app di-deploy di subfolder, mis. /ttd)
        $basePath = rtrim(BASE_PATH, '/');
        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = $this->normalize($uri === '' ? '/' : $uri);

        $method = strtoupper($method);
        $routes = $this->routes[$method] ?? [];

        // 1) Exact match dulu
        if (isset($routes[$uri])) {
            $this->call($routes[$uri], []);
            return;
        }

        // 2) Match dengan parameter dinamis, mis. /verify/{kode}
        foreach ($routes as $pattern => $handler) {
            $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                $this->call($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404');
    }

    private function call(array $handler, array $params): void
    {
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        call_user_func_array([$controller, $method], $params);
    }
}
