<?php

declare(strict_types=1);

namespace App\Notification;

use App\Statement\StatementRequest;
use RuntimeException;
use Throwable;

final readonly class FileNotifier implements Notifier
{
    public function __construct(
        private string $directory,
        private string $from,
    ) {
    }

    public static function fromEnv(): self
    {
        return new self(
            directory: getenv('MAIL_DIR') ?: __DIR__ . '/../../var/mail',
            from:      getenv('MAIL_FROM') ?: 'noreply@bank.local',
        );
    }

    public function notify(StatementRequest $request, string $statement): void
    {
        try {
            if (!is_dir($this->directory) && !mkdir($this->directory, 0777, true) && !is_dir($this->directory)) {
                throw new RuntimeException('Не удалось создать директорию: ' . $this->directory);
            }

            $subject = "Банковская выписка по запросу {$request->id}";
            $message = "From: {$this->from}\r\n"
                . "To: {$request->email}\r\n"
                . "Subject: {$subject}\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-Type: text/plain; charset=utf-8\r\n"
                . "\r\n"
                . $statement . "\r\n";

            $path = rtrim($this->directory, '/') . '/' . $request->id . '.eml';

            if (file_put_contents($path, $message) === false) {
                throw new RuntimeException('Не удалось записать файл: ' . $path);
            }

            echo '[email] сохранено в ' . $path . PHP_EOL;
        } catch (Throwable $e) {
            fwrite(STDERR, '[email] не удалось сохранить: ' . $e->getMessage() . PHP_EOL);
        }
    }
}
