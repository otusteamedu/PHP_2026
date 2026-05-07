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
            ]);

        return (array) $response->json();
    }

    public function sendMessage(int|string $chatId, string $text, ?array $replyMarkup = null): array
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = $replyMarkup;
        }

        $response = Http::timeout(10)->post("https://api.telegram.org/bot{$this->token}/sendMessage", $payload);

        return (array) $response->json();
    }

    public function answerCallbackQuery(string $callbackQueryId): array
    {
        $response = Http::timeout(10)->post("https://api.telegram.org/bot{$this->token}/answerCallbackQuery", [
            'callback_query_id' => $callbackQueryId,
        ]);

        return (array) $response->json();
    }
}
