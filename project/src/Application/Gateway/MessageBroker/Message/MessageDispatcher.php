<?php

declare(strict_types=1);

namespace App\Application\Gateway\MessageBroker\Message;

final readonly class MessageDispatcher
{
    /** @param MessageHandlerInterface[] $handlers */
    public function __construct(
        private array $handlers
    ) {
    }

    public function dispatch(MessageInterface $message): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($message);
        }
    }
}
