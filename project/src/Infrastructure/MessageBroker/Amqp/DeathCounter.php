<?php

declare(strict_types=1);

namespace App\Infrastructure\MessageBroker\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

/**
 * Чтение счётчика недоставок из заголовка x-death.
 *
 * Когда сообщение проходит через dead-letter (reject с requeue=false, истёкший TTL, переполнение очереди),
 * RabbitMQ сам дописывает в заголовки массив x-death.
 * Для каждой очереди, из которой сообщение "умирало", там лежит запись с полем count.
 */
final class DeathCounter
{
    /**
     * Сколько раз сообщение уже "умирало" (было отвергнуто / истёк TTL).
     * 0 - если это первая попытка (x-death ещё нет).
     */
    public static function attempts(AMQPMessage $message): int
    {
        $xDeath = self::xDeath($message);

        if ($xDeath === []) {
            return 0;
        }

        $maxCount = 0;

        foreach ($xDeath as $entry) {
            $count = isset($entry['count']) ? (int) $entry['count'] : 0;
            $maxCount = max($maxCount, $count);
        }

        return $maxCount;
    }

    /**
     * @return array<int|string>[]
     */
    public static function xDeath(AMQPMessage $message): array
    {
        if (!$message->has('application_headers')) {
            return [];
        }

        /** @var AMQPTable $headers */
        $headers = $message->get('application_headers');
        /** @var array<string, array<int|string>[]> $native */
        $native = $headers->getNativeData();

        return $native['x-death'] ?? [];
    }
}
