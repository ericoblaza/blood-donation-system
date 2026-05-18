<?php

declare(strict_types=1);

namespace Core\Http;

use App\Middleware\MiddlewareInterface;
use Core\Container\Container;

// Matches request method + path to a handler; runs middleware then controller.

class Router
{
    /** @var array<string, array<string, array{handler: callable|array, middleware: list<class-string>}> */
    private array $routes = [];

    public function __construct(private readonly ?Container $container = null)
    {
    }

    /**
     * @param list<class-string<MiddlewareInterface>> $middleware
     */
    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * @param list<class-string<MiddlewareInterface>> $middleware
     */
    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $path = $this->normalizePath($request->path());
        [$handler, $params, $middleware] = $this->resolve($method, $path);

        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        $request->setRouteParams($params);

        $this->runPipeline($request, $middleware, function (Request $req) use ($handler): void {
            $this->runHandler($handler, $req);
        });
    }

    /**
     * @param list<class-string<MiddlewareInterface>> $middleware
     */
    private function addRoute(string $method, string $path, callable|array $handler, array $middleware): void
    {
        $normalizedPath = $this->normalizePath($path);
        $this->routes[$method][$normalizedPath] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * @param list<class-string<MiddlewareInterface>> $middleware
     */
    private function runPipeline(Request $request, array $middleware, callable $destination): void
    {
        $next = $destination;

        foreach (array_reverse($middleware) as $middlewareClass) {
            $instance = new $middlewareClass();
            if (!$instance instanceof MiddlewareInterface) {
                http_response_code(500);
                echo "Invalid middleware: {$middlewareClass}";
                return;
            }

            $previous = $next;
            $next = static function (Request $req) use ($instance, $previous): void {
                $instance->handle($req, $previous);
            };
        }

        $next($request);
    }

    private function runHandler(callable|array $handler, Request $request): void
    {
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
     * @return array{0: callable|array|null, 1: array<string, string>, 2: list<class-string<MiddlewareInterface>>}
     */
    private function resolve(string $method, string $path): array
    {
        $methodRoutes = $this->routes[$method] ?? [];

        if (isset($methodRoutes[$path])) {
            return [
                $methodRoutes[$path]['handler'],
                [],
                $methodRoutes[$path]['middleware'],
            ];
        }

        foreach ($methodRoutes as $routePath => $route) {
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

            return [$route['handler'], $params, $route['middleware']];
        }

        return [null, [], []];
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
