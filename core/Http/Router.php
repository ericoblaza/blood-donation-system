<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Container\Container;

// Matches request method + path to a handler; runs controller action or returns 404/500.

class Router
{
    private array $routes = [];

    public function __construct(private readonly ?Container $container = null)
    {
    }

    public function get(string $path, callable|array $handler): void
    {
        $normalizedPath = $this->normalizePath($path);
        $this->routes['GET'][$normalizedPath] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $normalizedPath = $this->normalizePath($path);
        $this->routes['POST'][$normalizedPath] = $handler;
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $path = $this->normalizePath($request->path());
        [$handler, $params] = $this->resolve($method, $path);

        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        $request->setRouteParams($params);

        if (is_callable($handler)) {
            $handler($request);
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $action] = $handler;

            if (!class_exists($class)) {
                http_response_code(500);
                echo "Controller class not found: {$class}";
                return;
            }

            $controller = $this->container !== null
                ? $this->container->resolve($class)
                : new $class();

            if (!method_exists($controller, (string) $action)) {
                http_response_code(500);
                echo "Method not found: {$action}";
                return;
            }

            $controller->{$action}($request);
            return;
        }

        http_response_code(500);
        echo 'Invalid route handler';
    }

    /**
     * @return array{0: callable|array|null, 1: array<string, string>}
     */
    private function resolve(string $method, string $path): array
    {
        $methodRoutes = $this->routes[$method] ?? [];

        if (isset($methodRoutes[$path])) {
            return [$methodRoutes[$path], []];
        }

        foreach ($methodRoutes as $routePath => $handler) {
            if (!str_contains($routePath, '{')) {
                continue;
            }

            $paramNames = [];
            $pattern = preg_replace_callback(
                '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                static function (array $matches) use (&$paramNames): string {
                    $paramNames[] = $matches[1];

                    return '([^/]+)';
                },
                $routePath
            );

            if ($pattern === null || preg_match('#^' . $pattern . '$#', $path, $matches) !== 1) {
                continue;
            }

            array_shift($matches);
            $params = [];

            foreach ($paramNames as $index => $name) {
                $params[$name] = $matches[$index] ?? '';
            }

            return [$handler, $params];
        }

        return [null, []];
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
