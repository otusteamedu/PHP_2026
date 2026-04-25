<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\AnalyticEvent;
use App\Domain\ValueObject\Conditions;

interface EventRepositoryInterface
{
    /**
     * @return AnalyticEvent[]
     */
    public function findByConditions(Conditions $conditions): array;

    public function save(AnalyticEvent $event): void;

}
