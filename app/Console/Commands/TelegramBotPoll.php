<?php

namespace App\Console\Commands;

use App\Services\TelegramBotClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TelegramBotPoll extends Command
{
    protected $signature = 'bot:poll {--sleep=1}';

    protected $description = 'Run Telegram long polling loop';

    public function handle(): int
    {
        $token = (string) config('services.telegram.bot_token', '');
        if ($token === '') {
            $this->error('Missing services.telegram.bot_token');

            return self::FAILURE;
        }

        $client = new TelegramBotClient($token);
        $sleep = max(0, (int) $this->option('sleep'));

        $offset = (int) Cache::get('telegram.bot.offset', 0);

        while (true) {
            $payload = $client->getUpdates($offset, 25);
            $ok = (bool) ($payload['ok'] ?? false);
            $updates = $ok ? (array) ($payload['result'] ?? []) : [];

            foreach ($updates as $update) {
                $updateId = (int) ($update['update_id'] ?? 0);
                $offset = max($offset, $updateId + 1);

                $message = (array) ($update['message'] ?? []);
                $text = trim((string) ($message['text'] ?? ''));
                $chatId = $message['chat']['id'] ?? null;
                if ($chatId === null) {
                    continue;
                }

                if ($text === '' || str_starts_with($text, '/start')) {
                    $client->sendMessage($chatId, 'Напиши запрос в формате: <b>php foreach</b> или <b>go goroutine</b>.');

                    continue;
                }

                $reply = $this->buildReplyForQuery($text);
                $client->sendMessage($chatId, $reply);
            }

            Cache::forever('telegram.bot.offset', $offset);

            if ($sleep > 0) {
                sleep($sleep);
            }
        }
    }

    private function buildReplyForQuery(string $text): string
    {
        [$language, $q] = $this->parseQuery($text);
        if ($language === null || $q === null) {
            return 'Формат: <b>язык запрос</b>. Например: <b>php foreach</b>.';
        }

        $response = Http::timeout(10)->get(url('/api/v1/constructs'), [
            'language' => $language,
            'q' => $q,
            'limit' => 5,
        ]);

        if (! $response->ok()) {
            return 'Ошибка поиска.';
        }

        $data = (array) $response->json('data');
        if (count($data) === 0) {
            return 'Ничего не найдено.';
        }

        if (count($data) === 1) {
            $slug = (string) ($data[0]['slug'] ?? '');
            $card = Http::timeout(10)->get(url("/api/v1/constructs/{$language}/{$slug}"));
            if (! $card->ok()) {
                return 'Ошибка загрузки карточки.';
            }

            $title = (string) $card->json('title');
            $summary = (string) ($card->json('summary') ?? '');
            $snippets = (array) ($card->json('snippets') ?? []);

            $parts = ["<b>{$title}</b>"];
            if ($summary !== '') {
                $parts[] = $summary;
            }
            if (isset($snippets[0]['code'])) {
                $parts[] = '<pre><code>'.e((string) $snippets[0]['code']).'</code></pre>';
            }

            return implode("\n\n", $parts);
        }

        $lines = ['Нашлось несколько вариантов:'];
        foreach ($data as $row) {
            $t = (string) ($row['title'] ?? '');
            $s = (string) ($row['slug'] ?? '');
            $lines[] = "- {$t} ({$language} {$s})";
        }

        return implode("\n", $lines);
    }

    private function parseQuery(string $text): array
    {
        $text = preg_replace('/\s+/', ' ', trim($text)) ?? '';
        if ($text === '') {
            return [null, null];
        }

        $parts = explode(' ', $text, 2);
        if (count($parts) < 2) {
            return [null, null];
        }

        $language = strtolower(trim($parts[0]));
        $q = trim($parts[1]);

        if ($language === '' || $q === '') {
            return [null, null];
        }

        return [$language, $q];
    }
}
