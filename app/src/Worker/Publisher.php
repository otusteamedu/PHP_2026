<?php

declare(strict_types=1);

namespace App\Worker;

use App\Amqp\Connection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

final readonly class Publisher
{
    public function __construct(
        private Connection $connection,
        private string $exchange,
        private string $queue
    ) {}

    public function publish(string $message): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($this->queue, false, true, false, false);

        $channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);

        $channel->queue_bind($this->queue, $this->exchange);

        $message = new AMQPMessage($message, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $this->exchange);

        $this->connection->close();
    }
}
