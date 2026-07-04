<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail;

use App\Application\Ports\EmailSenderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final readonly class MailerAdapter implements EmailSenderInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromAddress
    ) {}

    public function send(string $to, string $subject, string $body, string $pdfContent, string $fileName): void
    {
        $email = (new Email())
            ->from($this->fromAddress)
            ->to($to)
            ->subject($subject)
            ->text($body)
            ->attach($pdfContent, $fileName, 'application/pdf');

        $this->mailer->send($email);
    }
}