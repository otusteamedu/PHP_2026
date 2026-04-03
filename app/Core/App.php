<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Controller\Controller;
use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Service\EventService;
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
        $controller = new Controller(new EventService(StorageFactory::create()));

        $router->add('POST', '/api/events', function() use ($request, $controller) {
            return $controller->add($request);
        });
        $router->add('DELETE', '/api/events', function() use ($controller) {
            return $controller->clear();
        });
        $router->add('POST', '/api/events/match', function() use ($request, $controller) {
            return $controller->match($request);
        });

        try {
            $response = ($router->dispatch($request))();
        } catch (RuntimeException) {
            $response = new JsonResponse(['error' => 'Route not found'], 404);
        } catch (Throwable $e) {
            $response = new JsonResponse(['error' => $e->getMessage()], 500);
        }

        $response->send();
    }
}
