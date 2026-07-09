<?php

declare(strict_types=1);

namespace App\Handlers;

use PHPMailer\PHPMailer\PHPMailer;

final readonly class MailNotificationHandler implements HandlerInterface
{
    public function __construct(
        private PHPMailer $mailer,
    ) {}

    public function handle(array $data): void
    {
        $email = $data['email'] ?? '';
        $from = $data['from'] ?? '';
        $to = $data['to'] ?? '';

        $this->mailer->addAddress($email);
        $this->mailer->Subject = 'Запрос принят в работу';
        $this->mailer->Body = sprintf(
            "Здравствуйте!\n\nВаш запрос на период с %s по %s принят в работу.\n\nС уважением,\nСлужба уведомлений",
            $from,
            $to
        );
        $this->mailer->send();
    }
}   