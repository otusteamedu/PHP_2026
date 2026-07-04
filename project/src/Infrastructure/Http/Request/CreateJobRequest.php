<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request;

use App\Infrastructure\Http\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface;

final readonly class CreateJobRequest
{
    public function __construct(
        public string $name,
    ) {
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $body = $request->getParsedBody();

        if (!is_array($body)) {
            throw new ValidationException(['body' => 'Тело запроса должно быть JSON-объектом']);
        }

        $name = $body['name'] ?? null;

        if (!is_string($name) || trim($name) === '') {
            throw new ValidationException(['name' => 'Имя не может быть пустым']);
        }

        return new self(trim($name));
    }
}
