<?php

declare(strict_types=1);

namespace App\Infrastructure\Gateway\Notification;

use App\Application\Gateway\Notification\Data;
use App\Application\Gateway\Notification\NotifierInterface;
use RuntimeException;
use Webmozart\Assert\Assert;

final readonly class TelegramNotifier implements NotifierInterface
{
    private string $botToken;

    public function __construct(string $botToken) {
        Assert::notEmpty($botToken);
        $this->botToken = $botToken;
    }

    public function notify(Data $data): void
    {
        $chatId = $data->chatId;
        if ($chatId === null) {
            return;
        }

        $url = sprintf('https://api.telegram.org/bot%s/sendMessage', $this->botToken);
        $payload = json_encode([
            'chat_id' => $chatId->value,
            'text' => $data->message,
        ]);
        echo $chatId->value . PHP_EOL;

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'ignore_errors' => true,
                'timeout' => 10,
            ],
        ]);

        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            throw new RuntimeException('Failed to send telegram notification');
        }

        /** @var array{ok?: bool, description?: string} $decoded */
        $decoded = json_decode($response, true);
        if (($decoded['ok'] ?? false) !== true) {
            throw new RuntimeException($decoded['description'] ?? 'Telegram API error');
        }
        echo 'Сообщение в Telegram отправлено' . PHP_EOL;
    }
}
