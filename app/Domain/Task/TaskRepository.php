<?php

namespace App\Domain\Task;

use App\Models\Task;

interface TaskRepository
{
    public function save(TaskAggregate $aggregate): Task;

    public function findForOwner(int $taskId, int $ownerUserId): ?TaskAggregate;
}
