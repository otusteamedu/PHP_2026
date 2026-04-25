<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\AnalyticEvent;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Conditions;
use App\Infrastructure\Storage\Storage;

final readonly class EventsRepository implements EventRepositoryInterface
{
    public function __construct(private Storage $client)
    {
    }

    public function findByConditions(Conditions $conditions): array
    {
        // TODO: Implement findByConditions() method.
    }

    public function save(AnalyticEvent $event): void
    {
        // TODO: Implement save() method.
    }
}
