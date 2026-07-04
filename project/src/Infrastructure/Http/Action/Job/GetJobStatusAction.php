<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action\Job;

use App\Application\UseCase\Task\Query\GetTaskStatusHandler;
use App\Application\UseCase\Task\Query\GetTaskStatusQuery;
use App\Infrastructure\Http\Exception\ValidationException;
use App\Infrastructure\Http\Response\JsonResponse;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final readonly class GetJobStatusAction
{
    public function __construct(
        private GetTaskStatusHandler $handler,
    ) {
    }

    /**
     * @param array<string, string> $args
     * @throws JsonException
     */
    #[OA\Get(
        path: '/job/{id}',
        operationId: 'getJobStatus',
        description: 'Возвращает текущий статус задачи',
        summary: 'Получить статус задачи',
        tags: ['Job'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'UUID задачи',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Статус задачи',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'taskId',
                            type: 'string',
                            format: 'uuid',
                            example: '550e8400-e29b-41d4-a716-446655440000'
                        ),
                        new OA\Property(
                            property: 'status',
                            type: 'string',
                            example: 'Создана'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Задача не найдена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Задача не найдена'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Ошибка валидации'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: ['id' => 'Некорректный UUID задачи']
                        ),
                    ]
                )
            ),
        ]
    )]
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (!array_key_exists('id', $args)) {
            throw new ValidationException(['id' => 'Некорректный UUID задачи']);
        }

        $taskId = $args['id'];

        try {
            $queryResponse = $this->handler->handle(new GetTaskStatusQuery($taskId));
        } catch (Throwable $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }

        return new JsonResponse([
            'taskId' => $taskId,
            'status' => $queryResponse->status,
        ]);
    }
}
