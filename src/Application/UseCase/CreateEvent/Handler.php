<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateEvent;

use App\Domain\Entity\AnalyticEvent;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Conditions;
use App\Domain\ValueObject\Event;
use App\Domain\ValueObject\Priority;

final readonly class Handler
{
    public function __construct(
        private EventRepositoryInterface $events,
    ) {
    }

    public function __invoke(Request $event): void
    {
        $analyticEvent = new AnalyticEvent(
            Priority::create($event->priority),
            Conditions::create($event->conditions),
            Event::create($event->event),
        );

        $this->events->save($analyticEvent);
    }
}
