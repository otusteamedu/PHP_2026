<?php

declare(strict_types=1);

namespace App\Application\UseCase\Command\Notification;

final readonly class SendNotificationCommand
{
    public function __construct(
        public string $message,
        public ?string $email = null,
        public ?int $chatId = null,
    ) {
    }
}
