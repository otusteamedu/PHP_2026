<?php

declare(strict_types=1);

use App\Core\Queue\KafkaConsumer;
use App\Core\Queue\QueueConfig;
use App\Notification\ConsoleNotifier;
use App\Notification\FileNotifier;
use App\Statement\StatementGenerator;
use App\Statement\StatementRequest;

require __DIR__ . '/../bootstrap.php';

$config    = QueueConfig::fromEnv();
$consumer  = new KafkaConsumer($config);
$notifiers = [
    new ConsoleNotifier(),
    FileNotifier::fromEnv(),
];

echo '[worker] подписан на '. $config->topic . ', ожидаю сообщения...' . PHP_EOL;

$consumer->consume(static function (string $payload) use ($notifiers): void {
    $generator = new StatementGenerator();
    echo PHP_EOL . '[worker] получено сообщение из очереди:' . PHP_EOL;
    echo $payload . PHP_EOL;

    try {
        $request = StatementRequest::fromJson($payload);
    } catch (JsonException $e) {
        echo '[worker] ошибка при декодировании сообщения: ' . $e->getMessage() . PHP_EOL;
        return;
    }

    echo "[worker] формирую выписку для запроса {$request->id}..." . PHP_EOL;
    $statement = $generator->generate($request);

    foreach ($notifiers as $notifier) {
        $notifier->notify($request, $statement);
    }
});
