<?php

declare(strict_types=1);

use App\Amqp\Connection;
use App\Handlers\MailNotificationHandler;
use App\Mail\MailerFactory;
use App\Worker\Consumer;

require __DIR__ . '/../src/bootstrap.php';

$consumer = new Consumer(
    connection: Connection::fromEnv(),
    exchange: $_ENV['RABBITMQ_EXCHANGE'] ?? 'notifications',
    queue: $_ENV['RABBITMQ_QUEUE'] ?? 'mail_notifications',
    handlers: [
        new MailNotificationHandler(MailerFactory::create()),
    ],
);

$consumer->consume();
