<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Controller\StatementController;
use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Queue\KafkaProducer;
use App\Core\Queue\QueueConfig;
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
        $producer = new KafkaProducer(QueueConfig::fromEnv());
        $controller = new StatementController($producer);

        $router->add('GET', '/', static function() use ($controller): Response {
            return $controller->form();
        });

        $router->add('POST', '/statements', static function() use ($request, $controller): Response {
            return $controller->submit($request);
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
