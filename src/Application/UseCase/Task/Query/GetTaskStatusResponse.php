<?php

declare(strict_types=1);

namespace App\Application\UseCase\Task\Query;

final readonly class GetTaskStatusResponse
{
    public function __construct(
        public string $status,
    ) {
    }
}
