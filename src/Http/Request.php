<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Request
{
    public function __construct(
        private string $method,
        private array $post,
    ) {
    }

    public static function create(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_POST,
        );
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function post(string $key): mixed
    {
        return $this->post[$key] ?? null;
    }
}
