<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateEvent;

final readonly class Request
{
    public function __construct(
        public int $priority,
        public array $conditions,
        public string $event,
    ) {
    }
}
