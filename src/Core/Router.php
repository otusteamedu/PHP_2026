<?php

declare(strict_types=1);


namespace App\Core;

use App\Core\Http\Request;

final class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(Request $request): callable
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        if (!isset($this->routes[$method][$path])) {
            throw new \RuntimeException('Route not found');
        }

        return $this->routes[$method][$path];
    }
}
