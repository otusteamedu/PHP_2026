<?php

declare(strict_types=1);

use App\Application\Gateway\MessageBroker\Message\MessageDispatcher;
use App\Application\Gateway\MessageBroker\Message\ProcessTaskMessageHandler;
use App\Application\Gateway\MessageBroker\ProducerInterface;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Infrastructure\MessageBroker\Amqp\Connection;
use App\Infrastructure\MessageBroker\Worker\Producer;
use App\Infrastructure\Repository\TaskRepository;
use Predis\Client;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [
    Twig::class => static fn (): Twig => Twig::create('/app/src/Infrastructure/Http/Template', ['cache' => false]),
    Client::class => static function (): Client {
        static $connection = null;

        if ($connection === null) {
            $params = [
                'host' => getenv('REDIS_HOST') ?: 'redis',
                'port' => (int) (getenv('REDIS_PORT') ?: 6379),
            ];

            $password = getenv('REDIS_PASSWORD');
            if ($password !== false && $password !== '') {
                $params['password'] = $password;
            }

            $connection = new Client($params);
        }

        return $connection;
    },
    TaskRepositoryInterface::class => autowire(TaskRepository::class),
    ProducerInterface::class => autowire(Producer::class),

    Connection::class => static function (): Connection {
        static $connection = null;

        if ($connection === null) {
            $connection = new Connection(
                host: getenv('RABBITMQ_HOST') ?: 'rabbitmq',
                port: (int) (getenv('RABBITMQ_PORT') ?: 5672),
                user: getenv('RABBITMQ_USER') ?: 'guest',
                password: getenv('RABBITMQ_PASSWORD') ?: 'guest',
                vhost: getenv('RABBITMQ_VHOST') ?: '/',
            );
        }

        return $connection;
    },
    'message_handlers' => add([
        get(ProcessTaskMessageHandler::class),
    ]),
    MessageDispatcher::class => static function (ContainerInterface $c): MessageDispatcher {
        return new MessageDispatcher($c->get('message_handlers'));
    },
];
