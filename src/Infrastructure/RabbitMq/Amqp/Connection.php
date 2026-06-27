<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Amqp;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class Connection
{
    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $user,
        private readonly string $password,
        private readonly string $vhost = '/'
    ) {
    }

    public static function fromEnv(): self
    {
        return new self(
            host: getenv('RABBITMQ_HOST') ?: 'localhost',
            port: (int)(getenv('RABBITMQ_PORT') ?: 5672),
            user: getenv('RABBITMQ_USER') ?: 'guest',
            password: getenv('RABBITMQ_PASSWORD') ?: 'guest',
            vhost: getenv('RABBITMQ_VHOST') ?: '/'
        );
    }

    public function channel(): AMQPChannel
    {
        if (empty($this->channel)) {
            $this->channel = $this->connection()->channel();
        }

        return $this->channel;
    }

    /**
     * @throws Exception
     */
    public function connection(): AMQPStreamConnection
    {
        if (empty($this->connection)) {
            $this->connection = new AMQPStreamConnection(
                host: $this->host,
                port: $this->port,
                user: $this->user,
                password: $this->password,
                vhost: $this->vhost
            );
        }

        return $this->connection;
    }

    /**
     * @throws Exception
     */
    public function close(): void
    {
        $this->channel?->close();
        $this->connection?->close();
        $this->channel = null;
        $this->connection = null;
    }
}
