<?php

declare(strict_types=1);

namespace App\Core\Http\Controller;

use App\Core\Http\Dto\CreateMovieRequest;
use App\Core\Http\Dto\UpdateMovieRequest;
use App\Core\Http\InvalidRequestException;
use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Service\MovieService;
use InvalidArgumentException;

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
        private MovieService $service,
    ) {
    }

    /** GET /api/movies - Получить все фильмы */
    public function list(): JsonResponse
    {
        $collection = $this->service->getAll();

        return new JsonResponse([
            'data'  => $collection->toArray(),
            'total' => count($collection),
        ]);
    }

    /** GET /api/movies/{id} - Получить фильм по ID */
    public function getOne(int $id): JsonResponse
    {
        $movie = $this->service->getById($id);

        if ($movie === null) {
            return new JsonResponse(['error' => 'Movie not found'], 404);
        }

        return new JsonResponse(['data' => $movie->toArray()]);
    }

    /** POST /api/movies - Добавить фильм */
    public function create(Request $request): JsonResponse
    {
        try {
            $dto = CreateMovieRequest::fromArray($request->json());
            $movie = $this->service->create($dto);
        } catch (InvalidRequestException|InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse(['data' => $movie->toArray()], 201);
    }

    /** PUT /api/movies/{id} - Обновить фильм */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $dto = UpdateMovieRequest::fromArray($request->json());
            $movie = $this->service->update($id, $dto);
        } catch (InvalidRequestException|InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        if ($movie === null) {
            return new JsonResponse(['error' => 'Movie not found'], 404);
        }

        return new JsonResponse(['data' => $movie->toArray()]);
    }

    /** DELETE /api/movies/{id} - Удалить фильм */
    public function delete(int $id): JsonResponse
    {
        if (!$this->service->deleteById($id)) {
            return new JsonResponse(['error' => 'Movie not found'], 404);
        }

        return new JsonResponse(['success' => true]);
    }
}
