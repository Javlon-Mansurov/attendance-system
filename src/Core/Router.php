<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $pattern, array $handler): void
    {
        $this->routes[] = ['GET', $pattern, $handler];
    }

    public function post(string $pattern, array $handler): void
    {
        $this->routes[] = ['POST', $pattern, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        foreach ($this->routes as [$routeMethod, $pattern, $handler]) {
            if ($routeMethod !== $method) {
                continue;
            }
            $regex = '#^' . preg_replace('#\{id\}#', '(\d+)', $pattern) . '$#';
            if (preg_match($regex, $path, $matches)) {
                array_shift($matches);
                [$class, $action] = $handler;
                $controller = new $class();
                $args = array_map('intval', $matches);
                $controller->$action(...$args);
                return;
            }
        }

        http_response_code(404);
        echo (new View(BASE_PATH . '/views'))->render('errors/error', [
            'title'   => 'Саҳифа ёфт нашуд',
            'code'    => 404,
            'message' => 'Саҳифаи дархостшуда мавҷуд нест.',
        ]);
    }
}
