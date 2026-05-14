<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use RuntimeException;
use Throwable;

final class App
{
    /**
     * @throws \JsonException
     */
    public function run(): void
    {
        $request = new Request();
        $router = new Router();
        $container = new Container();

        $movieController = $container->movies()->controller();

        $router->add('GET', '/api/movies', function () use ($movieController): JsonResponse {
            return $movieController->list();
        });

        $router->add('GET', '/api/movies/{id}', function (array $p) use ($movieController): JsonResponse {
            return $movieController->getOne((int) $p['id']);
        });

        $router->add('POST', '/api/movies', function () use ($request, $movieController): JsonResponse {
            return $movieController->create($request);
        });

        $router->add('PUT', '/api/movies/{id}', function (array $p) use ($request, $movieController): JsonResponse {
            return $movieController->update((int) $p['id'], $request);
        });

        $router->add('DELETE', '/api/movies/{id}', function (array $p) use ($movieController): JsonResponse {
            return $movieController->delete((int) $p['id']);
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
