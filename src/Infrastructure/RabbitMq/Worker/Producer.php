<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Worker;

use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\TelegramChatId;
use App\Infrastructure\RabbitMq\Amqp\Connection;
use App\Infrastructure\RabbitMq\Amqp\Topology;
use PhpAmqpLib\Message\AMQPMessage;

final readonly class Producer
{
    public function __construct(
        private Connection $connection,
        private Topology $topology
    ) {
    }

    public static function create(): self
    {
        return new self(
            Connection::fromEnv(),
            new Topology()
        );
    }

    public function publish(Email $email, ?TelegramChatId $chatId = null): void
    {
        $channel = $this->connection->channel();
        $this->topology->declare($channel);

        $payload = [
            'email' => $email->value,
            'chatId' => $chatId?->value,
        ];

        $message = new AMQPMessage(
            body: json_encode($payload, JSON_UNESCAPED_UNICODE),
            properties: [
                'content_type' => 'application/json',
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
