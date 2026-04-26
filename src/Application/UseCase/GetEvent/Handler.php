<?php

declare(strict_types=1);

namespace App\Application\UseCase\GetEvent;

use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Conditions;

final readonly class Handler
{
    public function __construct(
        private EventRepositoryInterface $events,
    ) {
    }

    public function __invoke(Request $request): ?Response
    {
        $conditions = Conditions::create($request->conditions);
        $event = $this->events->findPriorityOneByConditions($conditions);

        return $event ? new Response($event->event->value) : null;
    }
}
