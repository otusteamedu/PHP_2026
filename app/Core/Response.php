<?php
namespace App\Core;

class Response
{
    private array $data;
    private int $statusCode;

    public function __construct(array $data, int $statusCode = 200)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public function send(): string
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }
}
