<?php

declare(strict_types=1);

namespace App\Core\Http;

use JsonException;

final class JsonResponse
{
    public function __construct(
        private array $data,
        private int $statusCode = 200
    ) {}

    /**
     * @throws JsonException
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');

        echo json_encode($this->data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
