<?php

declare(strict_types=1);

namespace App\Application\UseCase\Task\Command;

final readonly class ProcessTaskCommand
{
    public function __construct(
        public string $id,
        public string $name,
    ) {
    }
}
