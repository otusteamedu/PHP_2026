<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API для управления отложенными задачами',
    title: 'PHP 2026 API'
)]
#[OA\Server(
    url: 'http://localhost',
    description: 'Локальный сайт'
)]
#[OA\Tag(
    name: 'Job',
    description: 'Операции с задачами для обработки'
)]
final class OpenApiDefinition
{
}
