<?php

declare(strict_types=1);

namespace App\Application\Gateway\Notification;


use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\TelegramChatId;

final readonly class Data
{
    public function __construct(
        public string $message,
        public ?Email $email = null,
        public ?TelegramChatId $chatId = null,
    ) {}
}
