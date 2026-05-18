<?php

declare(strict_types=1);

namespace Core\Http;

// Incoming HTTP: method, URI/path (with optional base-path strip), GET/POST input, route params.

class Request
{
    private string $basePath;

    /** @var array<string, string> */
    private array $routeParams = [];

    public function __construct(?string $basePath = null)
    {
        if ($basePath !== null) {
            $this->basePath = rtrim(str_replace('\\', '/', $basePath), '/');
            return;
        }

        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($script));

        if ($dir === '/' || $dir === '.' || $dir === '') {
            $this->basePath = '';
            return;
        }

        $this->basePath = rtrim($dir, '/');
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    public function path(): string
    {
        $raw = parse_url($this->uri(), PHP_URL_PATH);
        $path = is_string($raw) && $raw !== '' ? $raw : '/';

        if ($this->basePath !== '' && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath)) ?: '/';
        }

        if ($path === '' || $path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }

        return $path;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_POST, $_GET);
    }

    /** @param array<string, string> $params */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function route(string $key, ?string $default = null): ?string
    {
        return $this->routeParams[$key] ?? $default;
    }
}