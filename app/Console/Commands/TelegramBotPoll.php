<?php

namespace App\Console\Commands;

use App\Models\Construct;
use App\Models\Language;
use App\Models\TelegramQueryLog;
use App\Services\TelegramBotClient;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;

class TelegramBotPoll extends Command
{
    protected $signature = 'bot:poll {--sleep=1} {--debug} {--reset-offset}';

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
        $debug = (bool) $this->option('debug');

        if ((bool) $this->option('reset-offset')) {
            Cache::forget('telegram.bot.offset');
            $this->info('telegram.bot.offset cleared');

            return self::SUCCESS;
        }

        $offset = (int) Cache::get('telegram.bot.offset', 0);
        if ($debug) {
            $this->info('Starting offset: '.$offset);
        }

        while (true) {
            try {
                $payload = $client->getUpdates($offset, 25);
            } catch (ConnectionException $e) {
                if ($debug) {
                    $this->warn('getUpdates connection error: '.$e->getMessage());
                }

                sleep(2);

                continue;
            }
            $ok = (bool) ($payload['ok'] ?? false);
            $updates = $ok ? (array) ($payload['result'] ?? []) : [];

            if ($debug && ! $ok) {
                $this->warn('getUpdates not ok: '.json_encode($payload));
            }

            foreach ($updates as $update) {
                $updateId = (int) ($update['update_id'] ?? 0);
                $offset = max($offset, $updateId + 1);

                if (isset($update['callback_query'])) {
                    $callback = (array) $update['callback_query'];
                    $callbackId = (string) ($callback['id'] ?? '');
                    $data = (string) ($callback['data'] ?? '');
                    $chatId = $callback['message']['chat']['id'] ?? null;
                    $username = $callback['from']['username'] ?? null;

                    if ($debug) {
                        $this->line('callback_query update_id='.$updateId.' data='.$data);
                    }

                    if ($callbackId !== '') {
                        $client->answerCallbackQuery($callbackId);
                    }

                    if ($chatId !== null) {
                        if (! $this->rateLimitOk((string) $chatId)) {
                            $client->sendMessage($chatId, 'Слишком часто. Попробуй чуть позже.');

                            continue;
                        }

                        [$reply, $markup] = $this->buildReplyForCallbackData($data);
                        $result = $client->sendMessage($chatId, $reply, $markup);
                        if ($debug) {
                            $this->line('sendMessage ok='.(int) ($result['ok'] ?? 0));
                            if (! ($result['ok'] ?? false)) {
                                $this->warn(json_encode($result, JSON_UNESCAPED_UNICODE));
                            }
                        }

                        $parts = explode(':', $data, 3);
                        if (count($parts) === 3 && $parts[0] === 'c') {
                            $this->logQuery(
                                (string) $chatId,
                                $username !== null ? (string) $username : null,
                                $data,
                                (string) $parts[1],
                                null,
                                1,
                                (string) $parts[2]
                            );
                        }
                    }

                    continue;
                }

                $message = (array) ($update['message'] ?? []);
                $text = trim((string) ($message['text'] ?? ''));
                $chatId = $message['chat']['id'] ?? null;
                $username = $message['from']['username'] ?? null;
                if ($chatId === null) {
                    continue;
                }

                if ($debug) {
                    $this->line('message update_id='.$updateId.' text='.$text);
                }

                if ($text === '' || str_starts_with($text, '/start')) {
                    if (! $this->rateLimitOk((string) $chatId)) {
                        $client->sendMessage($chatId, 'Слишком часто. Попробуй чуть позже.');

                        continue;
                    }

                    $result = $client->sendMessage($chatId, $this->helpText());
                    if ($debug) {
                        $this->line('sendMessage ok='.(int) ($result['ok'] ?? 0));
                        if (! ($result['ok'] ?? false)) {
                            $this->warn(json_encode($result, JSON_UNESCAPED_UNICODE));
                        }
                    }

                    continue;
                }

                if (str_starts_with($text, '/help')) {
                    if (! $this->rateLimitOk((string) $chatId)) {
                        $client->sendMessage($chatId, 'Слишком часто. Попробуй чуть позже.');

                        continue;
                    }

                    $client->sendMessage($chatId, $this->helpText());

                    continue;
                }

                if (! $this->rateLimitOk((string) $chatId)) {
                    $client->sendMessage($chatId, 'Слишком часто. Попробуй чуть позже.');

                    continue;
                }

                [$reply, $markup] = $this->buildReplyForQuery($text);
                $result = $client->sendMessage($chatId, $reply, $markup);
                if ($debug) {
                    $this->line('sendMessage ok='.(int) ($result['ok'] ?? 0));
                    if (! ($result['ok'] ?? false)) {
                        $this->warn(json_encode($result, JSON_UNESCAPED_UNICODE));
                    }
                }

                [$language, $query] = $this->parseQuery($text);
                $this->logQuery(
                    (string) $chatId,
                    $username !== null ? (string) $username : null,
                    $text,
                    $language,
                    $query,
                    null
                );
            }

            Cache::forever('telegram.bot.offset', $offset);

            if ($sleep > 0) {
                sleep($sleep);
            }
        }
    }

    private function buildReplyForQuery(string $text): array
    {
        [$language, $q] = $this->parseQuery($text);
        if ($language === null || $q === null) {
            return [$this->helpText(), null];
        }

        $languageId = Language::query()->where('code', $language)->value('id');
        if ($languageId === null) {
            return ['Ничего не найдено.', null];
        }

        $query = Construct::query()
            ->where('language_id', $languageId)
            ->with([
                'snippets:id,construct_id,title,code,sort',
                'links:id,construct_id,title,url,sort',
            ]);

        $q = trim($q);
        if ($q !== '') {
            $query->where(function ($q1) use ($q): void {
                $q1->where('title', 'like', '%'.$q.'%')
                    ->orWhere('summary', 'like', '%'.$q.'%')
                    ->orWhere('details', 'like', '%'.$q.'%')
                    ->orWhere('slug', 'like', '%'.$q.'%');
            });
        }

        $items = $query->orderBy('title')->limit(5)->get();
        if ($items->isEmpty()) {
            return ['Ничего не найдено.', null];
        }

        if ($items->count() === 1) {
            $c = $items->first();

            return $this->buildCardResponse($language, (string) $c->slug);
        }

        $keyboard = [];
        foreach ($items as $c) {
            $keyboard[] = [[
                'text' => $c->title,
                'callback_data' => 'c:'.$language.':'.$c->slug,
            ]];
        }

        return [
            'Нашлось несколько вариантов. Выбери нужный:',
            ['inline_keyboard' => $keyboard],
        ];
    }

    private function buildReplyForCallbackData(string $data): array
    {
        if (! str_starts_with($data, 'c:')) {
            return ['Неизвестная команда.', null];
        }

        $parts = explode(':', $data, 3);
        if (count($parts) !== 3) {
            return ['Неизвестная команда.', null];
        }

        $language = trim((string) $parts[1]);
        $slug = trim((string) $parts[2]);

        if ($language === '' || $slug === '') {
            return ['Неизвестная команда.', null];
        }

        return $this->buildCardResponse($language, $slug);
    }

    private function buildCardResponse(string $language, string $slug): array
    {
        $languageId = Language::query()->where('code', $language)->value('id');
        if ($languageId === null) {
            return ['Не найдено.', null];
        }

        $c = Construct::query()
            ->where('language_id', $languageId)
            ->where('slug', $slug)
            ->with([
                'snippets:id,construct_id,title,code,sort',
                'links:id,construct_id,title,url,sort',
            ])
            ->first();

        if ($c === null) {
            return ['Не найдено.', null];
        }

        $parts = ['<b>'.$c->title.'</b>'];

        if ((string) $c->summary !== '') {
            $parts[] = (string) $c->summary;
        }

        $details = $this->shortDetails((string) $c->details);
        if ($details !== '') {
            $parts[] = $details;
        }

        $snippet = $c->snippets->first();
        if ($snippet !== null) {
            $parts[] = '<pre><code>'.e((string) $snippet->code).'</code></pre>';
        }

        $link = $c->links->first();
        if ($link !== null) {
            $parts[] = '<a href="'.e((string) $link->url).'">'.e((string) ($link->title ?: $link->url)).'</a>';
        }

        $baseUrl = rtrim((string) config('app.url'), '/');
        $openUrl = $baseUrl.'/api/v1/constructs/'.$language.'/'.$slug;

        $markup = null;
        if ($baseUrl !== '' && ! preg_match('~^https?://(localhost|127\.0\.0\.1)(:\d+)?$~i', $baseUrl)) {
            $markup = [
                'inline_keyboard' => [
                    [[
                        'text' => 'Открыть в справочнике',
                        'url' => $openUrl,
                    ]],
                ],
            ];
        }

        return [implode("\n\n", $parts), $markup];
    }

    private function shortDetails(string $details): string
    {
        $details = trim(preg_replace('/\s+/', ' ', $details) ?? '');
        if ($details === '') {
            return '';
        }

        $maxLen = 400;
        if (mb_strlen($details) <= $maxLen) {
            return $details;
        }

        $cut = mb_substr($details, 0, $maxLen);
        $pos = mb_strrpos($cut, '.');
        if ($pos !== false && $pos > 120) {
            return rtrim(mb_substr($cut, 0, $pos + 1));
        }

        return rtrim($cut).'…';
    }

    private function helpText(): string
    {
        return implode("\n", [
            'Напиши запрос в формате: <b>язык конструкция</b>.',
            'Примеры: <b>php foreach</b>, <b>php try</b>, <b>go defer</b>.',
            '',
            'Команды: /help',
        ]);
    }

    private function rateLimitOk(string $chatId): bool
    {
        $key = 'telegram.bot.rate.'.$chatId;

        return Cache::add($key, 1, now()->addSeconds(1));
    }

    private function logQuery(
        string $chatId,
        ?string $username,
        string $text,
        ?string $language,
        ?string $query,
        ?int $resultsCount,
        ?string $selectedSlug = null
    ): void {
        TelegramQueryLog::query()->create([
            'chat_id' => $chatId,
            'username' => $username,
            'text' => mb_substr($text, 0, 255),
            'language' => $language,
            'query' => $query,
            'results_count' => $resultsCount !== null ? max(0, $resultsCount) : 0,
            'selected_slug' => $selectedSlug,
        ]);
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
