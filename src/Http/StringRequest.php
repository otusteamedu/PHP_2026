<?php

declare(strict_types=1);

namespace App\Http;

final readonly class StringRequest
{
    public function __construct(
        public string $method,
        public ?string $string = null,
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_POST['string'] ?? null,
        );
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }
}
