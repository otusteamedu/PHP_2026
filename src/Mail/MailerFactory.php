<?php

declare(strict_types=1);

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;

final class MailerFactory
{
    public static function create(): PHPMailer
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $_ENV['MAIL_HOST'] ?? 'smtp.yandex.ru';
        $mailer->Port = (int)($_ENV['MAIL_PORT'] ?? 587);
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->SMTPAutoTLS = true;
        $mailer->SMTPAuth = !empty($_ENV['MAIL_USERNAME']);
        $mailer->Username = $_ENV['MAIL_USERNAME'] ?? '';
        $mailer->Password = $_ENV['MAIL_PASSWORD'] ?? '';
        $mailer->setFrom(
            $_ENV['MAIL_FROM'] ?? 'notifications@localhost',
            $_ENV['MAIL_FROM_NAME'] ?? 'Notification Service'
        );

        return $mailer;
    }
}
