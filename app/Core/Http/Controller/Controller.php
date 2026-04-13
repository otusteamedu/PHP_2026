<?php

declare(strict_types=1);

namespace App\Core\Http\Controller;

use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Service\EventService;
use JsonException;

final readonly class Controller
{
    public function __construct(
        private EventService $service,
    ) {
    }

    public function add(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        if (!isset($data['priority'], $data['conditions'], $data['event'])) {
            return new JsonResponse(['error' => 'Required fields: priority, conditions, event'], 400);
        }

        $this->service->add(
            (int)$data['priority'],
            (array)$data['conditions'],
            (array)$data['event'],
        );

        return new JsonResponse(['success' => true]);
    }

    public function clear(): JsonResponse
    {
        $this->service->clear();

        return new JsonResponse(['success' => true]);
    }

    public function match(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        if (!isset($data['params']) || !is_array($data['params'])) {
            return new JsonResponse(['error' => 'Required field: params'], 400);
        }

        $result = $this->service->match($data['params']);

        if ($result === null) {
            return new JsonResponse(['error' => 'No matching event found'], 404);
        }

        return new JsonResponse(['event' => $result]);
    }
}