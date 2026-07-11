<?php

declare (strict_types = 1);

namespace App;

use App\HTTP\Routing\Route;
use App\HTTP\Routing\RouteDispatcher;
use DI\ContainerBuilder;

class Kernel
{
    public function run(): void
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(BASE_PATH . '/config/container.php');
        $container = $builder->build();

        require BASE_PATH . '/routes/api.php';

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $routes = match ($requestMethod) {
            'GET'   => Route::getRoutesGet(),
            'POST'  => Route::getRoutesPost(),
            default => []
        };

        foreach ($routes as $routeConfig) {
            $dispatcher = new RouteDispatcher($routeConfig, $container);
            if ($dispatcher->process()) {
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error'   => 'Маршрут не найден',
        ]);
        
        exit;
    }
}
