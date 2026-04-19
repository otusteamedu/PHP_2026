<?php

declare(strict_types=1);

namespace App\Core\Http\Controller;

use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Storage\MovieStorage;
use JsonException;

/**
 *
 * Routes:
 *   GET    /api/movies      - Получить все фильмы
 *   GET    /api/movies/{id} - Получить фильм по ID
 *   POST   /api/movies      - Добавить фильм
 *   PUT    /api/movies/{id} - Обновить фильм
 *   DELETE /api/movies/{id} - Удалить фильм
 */
final readonly class MovieController
{
    public function __construct(
        private MovieStorage $storage,
    ) {
    }

    /** GET /api/movies - Получить все фильмы */
    public function list(): JsonResponse
    {
        $collection = $this->storage->getAll();

        return new JsonResponse([
            'data'  => $collection->toArray(),
            'total' => count($collection),
        ]);
    }

    /** GET /api/movies/{id} - Получить фильм по ID */
    public function getOne(int $id): JsonResponse
    {
        $movie = $this->storage->getById($id);

        if ($movie === null) {
            return new JsonResponse(['error' => 'Movie not found'], 404);
        }

        return new JsonResponse(['data' => $movie->toArray()]);
    }

    /** POST /api/movies - Добавить фильм */
    public function create(Request $request): JsonResponse
    {
        try {
            $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        if (!isset($body['title'], $body['year'])) {
            return new JsonResponse(['error' => 'Required fields: title, year'], 400);
        }

        $movie = $this->storage->create(
            title:    (string) $body['title'],
            year:     (int) $body['year'],
            genre:    (string) ($body['genre'] ?? ''),
            director: (string) ($body['director'] ?? ''),
        );

        return new JsonResponse(['data' => $movie->toArray()], 201);
    }

    /** PUT /api/movies/{id} - Обновить фильм */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        if (empty($body)) {
            return new JsonResponse(['error' => 'Nothing to update'], 400);
        }

        $movie = $this->storage->updateById($id, $body);

        if ($movie === null) {
            return new JsonResponse(['error' => 'Movie not found'], 404);
        }

        return new JsonResponse(['data' => $movie->toArray()]);
    }

    /** DELETE /api/movies/{id} - Удалить фильм */
    public function delete(int $id): JsonResponse
    {
        if (!$this->storage->deleteById($id)) {
            return new JsonResponse(['error' => 'Movie not found'], 404);
        }

        return new JsonResponse(['success' => true]);
    }
}
