<?php

declare(strict_types=1);

namespace App\Application\UseCase\Task\Query;

use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\ValueObject\Id;
use DomainException;

final readonly class GetTaskStatusHandler
{
    public function __construct(
        public TaskRepositoryInterface $tasks,
    ) {
    }

    public function handle(GetTaskStatusQuery $query): GetTaskStatusResponse
    {
        $task = $this->tasks->find(new Id($query->taskId));
        if (!$task) {
            throw new DomainException('Задача не найдена');
        }

        return new GetTaskStatusResponse($task->getStatus()->title());
    }
}
