<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action\OpenApi;

use OpenApi\Generator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

final readonly class SpecAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $openapi = new Generator()->generate([
            '/app/src/Infrastructure/Http',
        ]);

        if ($openapi === null) {
            throw new RuntimeException('OpenAPI спецификация не генерируется.');
        }

        $response->getBody()->write($openapi->toJson());

        return $response->withHeader('Content-Type', 'application/json');
    }
}
