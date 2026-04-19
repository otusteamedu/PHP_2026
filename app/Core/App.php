<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Controller\MovieController;
use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use JsonException;
use RuntimeException;
use Throwable;

final class App
{
    /**
     * @throws JsonException
     */
    public function run(): void
    {
        $request = new Request();
        $router = new Router();
        $controller = new MovieController(StorageFactory::create());

        $router->add('GET', '/api/movies', function (array $p) use ($controller): JsonResponse {
            return $controller->list();
        });

        $router->add('GET', '/api/movies/{id}', function (array $p) use ($controller): JsonResponse {
            return $controller->getOne((int) $p['id']);
        });

        $router->add('POST', '/api/movies', function (array $p) use ($request, $controller): JsonResponse {
            return $controller->create($request);
        });

        $router->add('PUT', '/api/movies/{id}', function (array $p) use ($request, $controller): JsonResponse {
            return $controller->update((int) $p['id'], $request);
        });

        $router->add('DELETE', '/api/movies/{id}', function (array $p) use ($controller): JsonResponse {
            return $controller->delete((int) $p['id']);
        });

        try {
            [$handler, $params] = $router->dispatch($request);
            $response = $handler($params);
        } catch (RuntimeException) {
            $response = new JsonResponse(['error' => 'Route not found'], 404);
        } catch (Throwable $e) {
            $response = new JsonResponse(['error' => $e->getMessage()], 500);
        }

        $response->send();
    }
}
