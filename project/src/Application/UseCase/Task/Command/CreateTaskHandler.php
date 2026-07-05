<?php

declare(strict_types=1);

namespace App\Application\UseCase\Task\Command;

use App\Application\Gateway\MessageBroker\Message\ProcessTaskMessage;
use App\Application\Gateway\MessageBroker\ProducerInterface;
use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\ValueObject\Id;

final readonly class CreateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $tasks,
        private ProducerInterface $producer,
    ) {
    }

    public function handle(CreateTaskCommand $command): Id
    {
        $task = Task::new();

        //тут нужно использовать outbox паттерн, чтобы не потерять согласованность, если что-то упадет
        $this->tasks->save($task);
        $this->producer->publish(new ProcessTaskMessage($task->id->value, $command->name));

        return $task->id;
    }
}
