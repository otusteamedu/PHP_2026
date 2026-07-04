<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action\Job;

use App\Application\UseCase\Task\Command\CreateTaskCommand;
use App\Application\UseCase\Task\Command\CreateTaskHandler;
use App\Infrastructure\Http\Request\CreateJobRequest;
use App\Infrastructure\Http\Response\JsonResponse;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class CreateJobAction
{
    public function __construct(
        private CreateTaskHandler $handler,
    ) {
    }

    /**
     * @throws JsonException
     */
    #[OA\Post(
        path: '/job',
        operationId: 'createJob',
        description: 'Ставит новую задачу обработки в очередь',
        summary: 'Создать задачу',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'Имя пользователя'
                    ),
                ]
            )
        ),
        tags: ['Job'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Задача создана',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'taskId',
                            type: 'string',
                            format: 'uuid',
                            example: '550e8400-e29b-41d4-a716-446655440000'
                        ),
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
                            example: ['name' => 'Поле name обязательно']
                        ),
                    ]
                )
            ),
        ]
    )]
    public function __invoke(Request $request, Response $response): Response
    {
        $createJobRequest = CreateJobRequest::fromRequest($request);
        $taskId = $this->handler->handle(
            new CreateTaskCommand($createJobRequest->name)
        );

        return new JsonResponse(
            ['taskId' => $taskId->value],
            201
        );
    }
}
