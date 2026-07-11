<?php

declare(strict_types=1);

namespace App\Infrastructure\Health;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Throwable;

final class RabbitHealthChecker
{
    public function check(): bool
    {
        try {

            $connection = new AMQPStreamConnection(
                getenv('RABBITMQ_HOST'),
                5672,
                getenv('RABBITMQ_USER'),
                getenv('RABBITMQ_PASSWORD')
            );

            $connection->close();

            return true;

        } catch (Throwable) {
            return false;
        }
    }
}