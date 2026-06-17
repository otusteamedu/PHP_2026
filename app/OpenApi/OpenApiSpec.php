<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.0.0',
    info: new OA\Info(version: '1.0.0', title: 'Todo Tasks API'),
    servers: [
        new OA\Server(url: '/', description: 'API host'),
    ],
    tags: [
        new OA\Tag(name: 'Auth', description: 'Passport tokens'),
        new OA\Tag(name: 'Tasks', description: 'ToDo CRUD'),
    ],
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Laravel Passport access token',
)]
class OpenApiSpec {}
