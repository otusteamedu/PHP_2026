<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;
use Shangab\Middleware\ShangabSlimSwagger;
use Slim\Psr7\Response as SlimResponse;

final class StatementSwagger extends ShangabSlimSwagger
{
    public function process(ServerRequest $request, RequestHandlerInterface $handler): Response
    {
        if ($request->getUri()->getPath() === '/openapi') {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode($this->buildOpenApiSpec(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return $response->withHeader('Content-Type', 'application/json');
        }

        return parent::process($request, $handler);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOpenApiSpec(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'HW20 Statement API',
                'version' => '1.0.0',
                'description' => 'API для создания запросов на получение выписки и проверки статуса обработки.',
            ],
            'tags' => [
                [
                    'name' => 'Statements',
                    'description' => 'Операции с запросами на выписку',
                ],
            ],
            'paths' => [
                '/api/statements' => [
                    'post' => [
                        'tags' => ['Statements'],
                        'summary' => 'Создать запрос на выписку',
                        'description' => 'Создаёт новый запрос, сохраняет его в БД, отправляет email о принятии и ставит задачу в очередь RabbitMQ.',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/CreateStatementRequest',
                                    ],
                                    'example' => [
                                        'email' => 'user@example.com',
                                        'date_from' => '2026-01-01',
                                        'date_to' => '2026-01-31',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Запрос успешно создан',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/CreateStatementResponse',
                                        ],
                                    ],
                                ],
                            ],
                            '422' => [
                                'description' => 'Ошибка валидации',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/ValidationErrorResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/statements/{id}' => [
                    'get' => [
                        'tags' => ['Statements'],
                        'summary' => 'Получить статус выписки',
                        'description' => 'Возвращает текущий статус обработки запроса по идентификатору.',
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'description' => 'UUID запроса на выписку',
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'uuid',
                                    'example' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Статус запроса',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/StatementStatusResponse',
                                        ],
                                    ],
                                ],
                            ],
                            '400' => [
                                'description' => 'Некорректный идентификатор',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/ErrorResponse',
                                        ],
                                    ],
                                ],
                            ],
                            '404' => [
                                'description' => 'Запрос не найден',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/ErrorResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'components' => [
                'schemas' => [
                    'CreateStatementRequest' => [
                        'type' => 'object',
                        'required' => ['email', 'date_from', 'date_to'],
                        'properties' => [
                            'email' => [
                                'type' => 'string',
                                'format' => 'email',
                                'example' => 'user@example.com',
                            ],
                            'date_from' => [
                                'type' => 'string',
                                'format' => 'date',
                                'example' => '2026-01-01',
                            ],
                            'date_to' => [
                                'type' => 'string',
                                'format' => 'date',
                                'example' => '2026-01-31',
                            ],
                        ],
                    ],
                    'CreateStatementResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'string', 'format' => 'uuid'],
                            'status' => ['type' => 'string', 'example' => 'new'],
                            'status_label' => ['type' => 'string', 'example' => 'Новый'],
                            'message' => ['type' => 'string'],
                        ],
                    ],
                    'StatementStatusResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'string', 'format' => 'uuid'],
                            'email' => ['type' => 'string', 'format' => 'email'],
                            'date_from' => ['type' => 'string', 'format' => 'date'],
                            'date_to' => ['type' => 'string', 'format' => 'date'],
                            'status' => ['type' => 'string', 'enum' => ['new', 'processing', 'completed']],
                            'status_label' => ['type' => 'string', 'example' => 'В обработке'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'ValidationErrorResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'errors' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                    ],
                    'ErrorResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'error' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
