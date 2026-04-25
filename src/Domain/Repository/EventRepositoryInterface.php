<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\AnalyticEvent;
use App\Domain\ValueObject\Conditions;

interface EventRepositoryInterface
{
    public function findPriorityOneByConditions(Conditions $conditions): AnalyticEvent;

    public function save(AnalyticEvent $event): void;

}
