<?php

declare (strict_types = 1);

namespace App\Shared;

class ResponseParser
{
    public function parse(int $statusCode, array $payload): void
    {
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        
        exit();
    }
}
