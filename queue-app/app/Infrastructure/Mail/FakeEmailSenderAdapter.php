<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail;

use App\Application\Ports\EmailSenderInterface;
use Psr\Log\LoggerInterface;

final readonly class FakeEmailSenderAdapter implements EmailSenderInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function send(string $to, string $subject, string $body, string $pdfContent, string $fileName): void
    {
        $sizeKb = round(strlen($pdfContent) / 1024, 2);
        
        $message = sprintf(
            "\n[FAKE MAILER]\nКому: %s\nТема: %s\nPDF вложение: %s\nРазмер: %s KB\nПисьмо не отправлено, используется тестовый режим\n",
            $to,
            $subject,
            $fileName,
            $sizeKb
        );
       
        $this->logger->info($message);
    }
}