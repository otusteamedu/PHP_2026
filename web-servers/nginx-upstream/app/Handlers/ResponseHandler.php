<?php

declare(strict_types=1);

namespace App\Handlers;

class ResponseHandler
{
    public function send(int $status, string $message): void
    {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
    }
}
