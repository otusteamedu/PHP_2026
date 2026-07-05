<?php

declare(strict_types=1);

namespace App\Application\UseCase\Task\Command;

use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\ValueObject\Id;
use DomainException;
use Throwable;

final readonly class ProcessTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $tasks
    ) {
    }

    public function handle(ProcessTaskCommand $command): void
    {
        $task = $this->tasks->find(new Id($command->id));
        if (!$task) {
            throw new DomainException('Задача не найдена');
        }

        $task->start();
        $this->tasks->save($task);

        //какая-то долгая логика, по итогу либо завершаем, либо отклоняем при ошибках
        try {
            sleep(random_int(10, 30));
            $task->finish();
        } catch (Throwable $e) {
            $task->cancel();
        }

        $this->tasks->save($task);
    }
}
