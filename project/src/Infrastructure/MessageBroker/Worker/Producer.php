<?php

declare(strict_types=1);

namespace App\Infrastructure\MessageBroker\Worker;

use App\Application\Gateway\MessageBroker\Message\MessageInterface;
use App\Application\Gateway\MessageBroker\ProducerInterface;
use App\Infrastructure\MessageBroker\Amqp\Connection;
use App\Infrastructure\MessageBroker\Amqp\Topology;
use PhpAmqpLib\Message\AMQPMessage;

final readonly class Producer implements ProducerInterface
{
    public function __construct(
        private Connection $connection,
        private Topology $topology
    ) {
    }

    public function publish(MessageInterface $message): void
    {
        $channel = $this->connection->channel();
        $this->topology->declare($channel);


        $amqpMessage = new AMQPMessage(
            body: serialize($message),
            properties: [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $channel->basic_publish(
            msg: $amqpMessage,
            exchange: Topology::WORK_EXCHANGE,
            routing_key: $message->getRoutingKey(),
        );
    }
}
