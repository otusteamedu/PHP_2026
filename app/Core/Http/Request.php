<?php

declare(strict_types=1);

namespace App\Core\Http;

class Request
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getContent(): string
    {
        return file_get_contents('php://input') ?: '';
    }

    public function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function getPostParam(string $name): string
    {
        return trim((string) ($_POST[$name] ?? ''));
    }
}
