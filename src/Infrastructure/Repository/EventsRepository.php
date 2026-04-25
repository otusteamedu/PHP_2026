<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\AnalyticEvent;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Conditions;
use App\Domain\ValueObject\Event;
use App\Domain\ValueObject\Priority;
use App\Infrastructure\Storage\Storage;

final readonly class EventsRepository implements EventRepositoryInterface
{
    public function __construct(private Storage $client)
    {
    }

    public function save(AnalyticEvent $event): void
    {
        $this->client->set(
            $event->conditions->getSortedKey(),
            $event->priority->value,
            $event->event->value
        );
    }

    public function findPriorityOneByConditions(Conditions $conditions): ?AnalyticEvent
    {
        $keys = $conditions->getAllPossibleSortedKeys();

        $event = null;
        $key = null;
        $maxPriority = null;

        foreach ($keys as $sortedKey) {
            $eventData = $this->client->get($sortedKey);
            if (empty($eventData)) {
                continue;
            }

            $currentEvent = array_key_first($eventData);
            $priority = $eventData[$currentEvent];

            if ($maxPriority === null || $priority > $maxPriority) {
                $maxPriority = $priority;
                $event = $currentEvent;
                $key = $sortedKey;
            }
        }

        if ($event === null) {
            return null;
        }

        return new AnalyticEvent(
            Priority::create((int)$maxPriority),
            Conditions::fromKey($key),
            Event::create($event)
        );
    }
}
