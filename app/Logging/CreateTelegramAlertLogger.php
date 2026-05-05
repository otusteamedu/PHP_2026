<?php

namespace App\Logging;

use Monolog\Handler\FallbackGroupHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

class CreateTelegramAlertLogger
{
    public function __invoke(array $config): Logger
    {
        $token = (string) ($config['api_token'] ?? '');
        $channelId = (string) ($config['channel_id'] ?? '');
        $fallbackPath = (string) ($config['fallback_path'] ?? storage_path('logs/telegram-error-fallback.log'));
        $levelName = strtolower((string) ($config['level'] ?? 'error'));

        try {
            $minLevel = Level::fromName($levelName);
        } catch (\UnhandledMatchError) {
            $minLevel = Level::Error;
        }

        $processors = [new PsrLogMessageProcessor];

        $fallback = new StreamHandler($fallbackPath, $minLevel);

        if (! extension_loaded('curl') || $token === '' || $channelId === '') {
            return new Logger('telegram_alerts', [$fallback], $processors);
        }

        $telegram = new TelegramBotHandler($token, $channelId, $minLevel);

        return new Logger('telegram_alerts', [
            new FallbackGroupHandler([$telegram, $fallback]),
        ], $processors);
    }
}
