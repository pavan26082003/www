<?php

declare(strict_types=1);

namespace NasApi\Core;

final class Router
{
    /** @var array<string,array<int,array{path:string,regex:string,handler:callable}>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    private function add(string $method, string $path, callable $handler): void
    {
        $method = strtoupper($method);
        $normalized = $this->normalize($path);
        $this->routes[$method][] = [
            'path' => $normalized,
            'regex' => $this->compile($normalized),
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $path = $this->requestPath();

        $methodRoutes = $this->routes[$method] ?? [];

        foreach ($methodRoutes as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $params = [];
            foreach ($matches as $name => $value) {
                if (is_string($name)) {
                    $params[$name] = rawurldecode((string) $value);
                }
            }

            ($route['handler'])($params);
            return;
        }

        // If this path exists for another method, return 405 rather than 404.
        foreach ($this->routes as $registeredMethod => $routes) {
            if ($registeredMethod === $method) {
                continue;
            }

            foreach ($routes as $route) {
                if (preg_match($route['regex'], $path)) {
                    JsonResponse::send([
                        'success' => false,
                        'error' => 'method_not_allowed',
                        'message' => 'Method not allowed.',
                    ], 405);
                }
            }
        }

        JsonResponse::send([
            'success' => false,
            'error' => 'not_found',
            'message' => 'API endpoint not found.',
        ], 404);
    }

    private function requestPath(): string
    {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = (string) (parse_url($uri, PHP_URL_PATH) ?: '/');
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/nas_api/index.php'));

        // Supports /nas_api/index.php/v1/... when mod_rewrite is unavailable.
        if ($scriptName !== '' && strncmp($path, $scriptName, strlen($scriptName)) === 0) {
            $path = substr($path, strlen($scriptName));
        } else {
            $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
            if ($base !== '' && $base !== '/' && strncmp($path, $base . '/', strlen($base . '/')) === 0) {
                $path = substr($path, strlen($base));
            }
        }

        return $this->normalize($path);
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }

    private function compile(string $path): string
    {
        $quoted = preg_quote($path, '#');

        // Convert escaped {name} placeholders into named path parameters.
        $regex = preg_replace_callback(
            '/\\\\\{([A-Za-z_][A-Za-z0-9_]*)\\\\\}/',
            static fn (array $m): string => '(?P<' . $m[1] . '>[^/]+)',
            $quoted
        );

        return '#^' . ($regex ?? $quoted) . '$#';
    }
}
