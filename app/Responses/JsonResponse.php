<?php

namespace App\Responses;

class JsonResponse
{
    private int $statusCode;
    private array $data;

    public function __construct(int $statusCode, array $data)
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    public function getJson(): string
    {
        http_response_code($this->statusCode);
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }
}