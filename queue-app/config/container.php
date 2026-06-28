<?php

use App\Application\Interfaces\QueuePublisherInterface;
use App\Application\Ports\EmailSenderInterface;
use App\Application\Ports\PdfGeneratorInterface;
use App\Application\UseCases\EnqueueEmailValidationUseCase;
use App\Domain\EmailValidator;
use App\Infrastructure\Mail\FakeEmailSenderAdapter;
use App\Infrastructure\Mail\MailerAdapter;
use App\Infrastructure\Pdf\PdfGeneratorAdapter;
use App\Infrastructure\Queue\Connection;
use App\Infrastructure\Queue\RabbitMqPublisher;
use App\Infrastructure\Validation\EmailValidation;
use App\Infrastructure\Validation\MxValidation;
use function DI\create;
use function DI\factory;
use function DI\get;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

$appConfig = require __DIR__ . '/app.php';

return [
    Connection::class                    => factory([Connection::class, 'fromEnv']),

    EmailValidator::class                => factory(function () {
        return new EmailValidator([new EmailValidation(), new MxValidation()]);
    }),

    EnqueueEmailValidationUseCase::class => create(EnqueueEmailValidationUseCase::class)
        ->constructor(
            get(EmailValidator::class),
            get(RabbitMqPublisher::class),
            $appConfig['max_emails_in_request'] ?? 10
        ),

    PdfGeneratorInterface::class         => get(PdfGeneratorAdapter::class),

    QueuePublisherInterface::class       => get(RabbitMqPublisher::class),

    FakeEmailSenderAdapter::class        => create(FakeEmailSenderAdapter::class),

    EmailSenderInterface::class          => factory(
        function (ContainerInterface $c) use ($appConfig) {

            $driver = strtolower($appConfig['mail_driver'] ?? 'fake');

            return match ($driver) {
                'smtp'  => $c->get(MailerAdapter::class),
                'fake'  => $c->get(FakeEmailSenderAdapter::class),
                default => throw new RuntimeException(
                    "Unknown mail driver: {$driver}"
                ),
            };
        }
    ),

    MailerAdapter::class => create(MailerAdapter::class)->constructor(get(MailerInterface::class), $appConfig['mail_from']),

    MailerInterface::class => factory(function () use ($appConfig) {$transport = Transport::fromDsn($appConfig['smtp_dsn']);
        return new Mailer($transport);}),

    LoggerInterface::class => factory(function () {
        return new ConsoleLogger(new ConsoleOutput());
    }),
];
