<?php
namespace App\Core;

class Kernel
{
    private Request $request;
    private array $routes;

    public function __construct()
    {
        $this->request = new Request;
        $this->routes = $this->loadRoutes();
    }

    private function loadRoutes(): array
    {
        $routesFile = __DIR__ . '/../../routes/api.php';
        if (!file_exists($routesFile)) {
            throw new \RuntimeException('Routes configuration file not found: ' . $routesFile);
        }
        return require $routesFile;
    }

    public function handle(): string
{
    [$routeKey, $params] = $this->matchRoute();

    if (!$routeKey) {
        return (new Response(['error' => 'Not Found'], 404))->send();
    }

    $controllerInfo = $this->routes[$routeKey];
    $controllerClass = is_array($controllerInfo) ? $controllerInfo[0] : $controllerInfo;
    $method = is_array($controllerInfo) ? ($controllerInfo[1] ?? '__invoke') : '__invoke';

    try {
        $controller = new $controllerClass();

        if (method_exists($controller, $method)) {
            // Получаем Reflection метода для анализа параметров
            $reflection = new \ReflectionMethod($controllerClass, $method);
            $methodParams = $reflection->getParameters();

            // Подготавливаем аргументы для вызова метода
            $args = [];

            foreach ($methodParams as $param) {
                // Если параметр — это Request, передаём наш объект
                if ($param->getType()?->getName() === Request::class) {
                    $args[] = $this->request;
                }
                // Если это параметр маршрута (например, $id), берём из $params
                elseif (isset($params[$param->getName()])) {
                    // Приводим к нужному типу, если указано
                    $value = $params[$param->getName()];
                    if ($param->getType()) {
                        $type = $param->getType()->getName();
                        if ($type === 'int') {
                            $value = (int)$value;
                        }
                    }
                    $args[] = $value;
                }
                // Для остальных параметров используем значение по умолчанию или null
                else {
                    if ($param->isDefaultValueAvailable()) {
                        $args[] = $param->getDefaultValue();
                    } else {
                        $args[] = null;
                    }
                }
            }

            $result = $controller->$method(...$args);

            if ($result instanceof Response) {
                return $result->send();
            }
            return (new Response($result))->send();
        } else {
            return (new Response(['error' => 'Method not found'], 500))->send();
        }
    } catch (\Exception $e) {
        return (new Response(['error' => $e->getMessage()], 500))->send();
    }
}

    private function matchRoute(): array
    {
        $requestMethod = $this->request->getMethod();
        $requestUri = $this->request->getUri();

        foreach ($this->routes as $routePattern => $controller) {
            [$method, $pattern] = explode(' ', $routePattern, 2);

            if ($method !== $requestMethod) {
                continue;
            }

            // Поддержка параметров в URL: /api/users/{id}
            $patternRegex = preg_replace('/\{([a-zA-Z_]+\w*)\}/', '(?P<$1>[^/]+)', $pattern);
            $patternRegex = '~^' . $patternRegex . '$~';

            if (preg_match($patternRegex, $requestUri, $matches)) {
                // Убираем полные совпадения, оставляем только именованные параметры
                array_shift($matches);
                return [$routePattern, $matches];
            }
        }

        return [null, []];
    }
}
