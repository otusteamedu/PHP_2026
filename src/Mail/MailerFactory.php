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
        $mailer->Host = $_ENV['MAIL_HOST'] ?: 'localhost';
        $mailer->SMTPAuth = true;
        $mailer->Username = $_ENV['MAIL_USERNAME'] ?: '';
        $mailer->Password = $_ENV['MAIL_PASSWORD'] ?: '';
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->Port = (int)($_ENV['MAIL_PORT'] ?: 465);
        $mailer->CharSet = 'UTF-8';
        $mailer->setFrom(
            $_ENV['MAIL_FROM'] ?: $_ENV['MAIL_USERNAME'] ?: 'noreply@example.com',
            $_ENV['MAIL_FROM_NAME'] ?: 'Statement Service',
        );

        return $mailer;
    }
}
