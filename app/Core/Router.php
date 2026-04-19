<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Request;
use RuntimeException;

final class Router
{
    /** @var array<string, list<array{pattern: array{regex: string, params: list<string>}, handler: callable}>> */
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][] = [
            'pattern' => $this->compile($path),
            'handler' => $handler,
        ];
    }

    /**
     * Returns [callable $handler, array $params] for the matched route.
     *
     * @return array{0: callable, 1: array<string, string>}
     * @throws RuntimeException when no route matches
     */
    public function dispatch(Request $request): array
    {
        $method = $request->getMethod();
        $path   = $request->getPath();

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern']['regex'], $path, $matches)) {
                $params = [];
                foreach ($route['pattern']['params'] as $i => $name) {
                    $params[$name] = $matches[$i + 1];
                }

                return [$route['handler'], $params];
            }
        }

        throw new RuntimeException('Route not found');
    }

    /**
     * Converts a path template like /api/events/{id} into a regex
     * and a list of parameter names.
     *
     * @return array{regex: string, params: list<string>}
     */
    private function compile(string $path): array
    {
        $params = [];

        $regex = preg_replace_callback('/\{([^}]+)\}/', static function (array $m) use (&$params): string {
            $params[] = $m[1];
            return '([^/]+)';
        }, $path);

        return [
            'regex'  => '#^' . $regex . '$#',
            'params' => $params,
        ];
    }
}
