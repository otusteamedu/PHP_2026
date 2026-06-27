<?php

declare(strict_types=1);

namespace App\Application\UseCase\Command\Notification;


use App\Application\Gateway\Notification\Data;
use App\Application\Gateway\Notification\NotifierInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\TelegramChatId;

final readonly class SendNotificationHandler
{
    /**
     * @param NotifierInterface[] $notifiers
     */
    public function __construct(
        private array $notifiers,
    ) {
    }

    public function handle(SendNotificationCommand $command): void
    {
        $email = $command->email ? new Email($command->email) : null;
        $chatId = $command->chatId ? new TelegramChatId($command->chatId) : null;
        $data = new Data($command->message, $email, $chatId);

        foreach ($this->notifiers as $notifier) {
            $notifier->notify($data);
        }
    }
}
