<?php

declare (strict_types = 1);

namespace App\Infrastructure\Queue;

use App\Application\DTO\ValidationJobPayload;
use App\Application\Interfaces\QueuePublisherInterface;
use PhpAmqpLib\Message\AMQPMessage;

final readonly class RabbitMqPublisher implements QueuePublisherInterface
{
    public function __construct(
        private Connection $connection,
        private Topology $topology
    ) {}

    public function publish(ValidationJobPayload $payload): void
    {
        $channel = $this->connection->channel();
       
        $this->topology->declare($channel);
       
        $body = json_encode($payload->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $message = new AMQPMessage(
            body: $body,
            properties: [
                'content_type'  => 'application/json',                
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $channel->basic_publish(
            msg: $message,
            exchange: Topology::WORK_EXCHANGE,
            routing_key: Topology::ROUTING_KEY
        );
    }
}
