<?php

declare(strict_types=1);

namespace App\Application\UseCase\ClearEvents;

use App\Domain\Repository\EventRepositoryInterface;

final readonly class Handler
{
    public function __construct(
        private EventRepositoryInterface $events,
    ) {
    }

    public function __invoke(): void
    {
        $this->events->deleteAll();
    }
}
