<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\ValidationException;
use App\Core\Http\Controller\BracketController;
use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Core\Service\BracketValidator;
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

        $router->add('POST', '/validate', function () use ($request) {
            $controller = new BracketController(new BracketValidator());
            return $controller->validate($request);
        });

        try {
            $handler = $router->dispatch($request);
            $response = $handler();

        } catch (ValidationException $e) {
            $response = new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);

        } catch (RuntimeException $e) {
            $response = new JsonResponse([
                'success' => false,
                'error' => 'Route not found'
            ], 404);

        } catch (Throwable $e) {
            $response = new JsonResponse([
                'success' => false,
                'error' => 'Internal Server Error'
            ], 500);
        }

        $response->send();
    }
}
