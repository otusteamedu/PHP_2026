<?php

declare(strict_types=1);

namespace App\Infrastructure\Gateway\Notification;

use App\Application\Gateway\Notification\NotifierInterface;
use App\Domain\ValueObject\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class NotifiersFactory
{
    /**
     * @return NotifierInterface[]
     */
    public static function fromEnv(): array
    {
        $notifiers = [];
        $mailer = new Mailer(Transport::fromDsn(getenv('MAILER_DSN') ?: 'null://null'));
        $notifiers[] = new EmailNotifier(
            mailer: $mailer,
            from: new Email(getenv('MAILER_FROM') ?: 'app@localhost'),
        );

        if ($telegramBotToken = getenv('TELEGRAM_BOT_TOKEN') ?: false) {
            $notifiers[] = new TelegramNotifier($telegramBotToken);
        };

        return $notifiers;
    }
}
