<?php

declare(strict_types=1);

namespace App\Application\Gateway\MessageBroker\Message;

use App\Application\UseCase\Task\Command\ProcessTaskCommand;
use App\Application\UseCase\Task\Command\ProcessTaskHandler;

final readonly class ProcessTaskMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private ProcessTaskHandler $handler
    ) {
    }

    public function handle(MessageInterface $message): void
    {
        if ($message instanceof ProcessTaskMessage) {
            $command = new ProcessTaskCommand($message->taskId, $message->userName);
            $this->handler->handle($command);
        }
    }
}
