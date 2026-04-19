<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class Event
{
    public function __construct(
        public int $priority,
        public array $conditions,
        public mixed $event,
    )
    {
    }
}
