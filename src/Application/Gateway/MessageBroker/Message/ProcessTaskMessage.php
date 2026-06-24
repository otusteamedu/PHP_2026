<?php

declare(strict_types=1);

namespace App\Application\Gateway\MessageBroker\Message;

use App\Infrastructure\MessageBroker\Amqp\Topology;

final readonly class ProcessTaskMessage implements MessageInterface
{
    public function __construct(
        public string $taskId,
        public string $userName,
    ) {
    }

    public function getRoutingKey(): string
    {
        return Topology::ROUTING_KEY;
    }
}
