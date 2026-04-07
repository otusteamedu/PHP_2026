<?php

namespace App\Core;

class Request
{
    public static function createFromGlobals(): self
    {
        return new self();
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $_POST[$key] ?? $default;
    }

    public function isJson(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_contains($contentType, 'application/json');
    }

    public function getJsonBody(): array
    {
        if (!$this->isJson()) {
            return [];
        }

        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
}
