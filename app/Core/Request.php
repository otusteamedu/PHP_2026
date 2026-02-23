<?php
// src/Core/Request.php

namespace App\Core;

class Request
{
    public string $method;
    public string $uri;
    public array $params = [];
    public array $get = [];
    public array $post = [];
    public array $server = [];

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }

    public static function createFromGlobals(): self
    {
        return new self();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $this->post[$key] ?? $default;
    }

    public function isJson(): bool
    {
        $contentType = $this->server['CONTENT_TYPE'] ?? '';
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
