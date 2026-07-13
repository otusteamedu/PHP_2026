<?php

declare(strict_types=1);

use App\Amqp\Connection;
use App\Database\Connection as DatabaseConnection;
use App\Handlers\ProcessNewStatementHandler;
use App\Mail\MailerFactory;
use App\Mail\StatementNotificationService;
use App\Repository\StatementRequestRepository;
use App\Worker\Consumer;
use App\Worker\Publisher;

require __DIR__ . '/../src/bootstrap.php';

$repository = new StatementRequestRepository(DatabaseConnection::fromEnv());
$notifications = new StatementNotificationService(MailerFactory::create());

$consumer = new Consumer(
    connection: Connection::fromEnv(),
    exchange: $_ENV['RABBITMQ_EXCHANGE'] ?: 'statements',
    queue: $_ENV['RABBITMQ_QUEUE_NEW'] ?: 'statement_new',
    handlers: [
        new ProcessNewStatementHandler(
            repository: $repository,
            processingPublisher: new Publisher(
                connection: Connection::fromEnv(),
                exchange: $_ENV['RABBITMQ_EXCHANGE'] ?: 'statements',
                queue: $_ENV['RABBITMQ_QUEUE_PROCESSING'] ?: 'statement_processing',
            ),
            notifications: $notifications,
        ),
    ],
);

$consumer->consume();
