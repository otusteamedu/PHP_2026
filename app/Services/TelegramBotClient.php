<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramBotClient
{
    public function __construct(
        private readonly string $token
    ) {}

    public function getUpdates(int $offset, int $timeoutSeconds = 25): array
    {
        $response = Http::timeout($timeoutSeconds + 5)
            ->get("https://api.telegram.org/bot{$this->token}/getUpdates", [
                'offset' => $offset,
                'timeout' => $timeoutSeconds,
                'allowed_updates' => ['message'],
            ]);

        return (array) $response->json();
    }

    public function sendMessage(int|string $chatId, string $text): array
    {
        $response = Http::timeout(10)->post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);

        return (array) $response->json();
    }
}
