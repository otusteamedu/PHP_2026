<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Task;
use App\Domain\ValueObject\Id;

interface TaskRepositoryInterface
{
    public function find(Id $id): ?Task;
    public function save(Task $task): void;
}
