<?php

declare(strict_types=1);

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;

final readonly class StatementNotificationService
{
    public function __construct(
        private PHPMailer $mailer,
    ) {}

    public function sendAccepted(string $email, string $dateFrom, string $dateTo, string $id): void
    {
        $this->send(
            email: $email,
            subject: 'Запрос на выписку принят',
            body: sprintf(
                "Здравствуйте!\n\nВаш запрос на выписку за период с %s по %s принят.\nНомер заявки: %s\n\nС уважением,\nСлужба уведомлений",
                $dateFrom,
                $dateTo,
                $id,
            ),
        );
    }

    public function sendProcessing(string $email, string $dateFrom, string $dateTo, string $id): void
    {
        $this->send(
            email: $email,
            subject: 'Заявление находится в обработке',
            body: sprintf(
                "Здравствуйте!\n\nВаше заявление на выписку за период с %s по %s находится в обработке.\nНомер заявки: %s\n\nС уважением,\nСлужба уведомлений",
                $dateFrom,
                $dateTo,
                $id,
            ),
        );
    }

    public function sendCompleted(string $email, string $dateFrom, string $dateTo, string $id): void
    {
        $this->send(
            email: $email,
            subject: 'Обработка выписки завершена',
            body: sprintf(
                "Здравствуйте!\n\nОбработка вашей выписки за период с %s по %s завершена.\nНомер заявки: %s\n\nС уважением,\nСлужба уведомлений",
                $dateFrom,
                $dateTo,
                $id,
            ),
        );
    }

    private function send(string $email, string $subject, string $body): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($email);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        $this->mailer->send();
    }
}
