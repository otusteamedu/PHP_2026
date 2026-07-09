<?php

declare(strict_types=1);

namespace App\Worker;

use App\Amqp\Connection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

final readonly class Consumer
{
    public function __construct(
        private Connection $connection,
        private string $exchange,
        private string $queue,
        private array $handlers = [],
    ) {}

    public function consume(): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($this->queue, false, true, false, false);
        $channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);
        $channel->queue_bind($this->queue, $this->exchange);
        $channel->basic_qos(null, 1, null);
        $channel->basic_consume(
            queue: $this->queue,
            callback: fn(AMQPMessage $message) => $this->processMessage($message),
            no_ack: false,
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    private function processMessage(AMQPMessage $message): void
    {
        $body = json_decode($message->body, true);
        if (!is_array($body)) {
            $message->nack(false, false);
            return;
        }

        foreach ($this->handlers as $handler) {
            $handler->handle($body);
        }

        $message->ack();
    }
}
