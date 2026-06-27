<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;

final class Topology
{
    /**
     * Основной обмен: сюда публикует producer.
     */
    public const string WORK_EXCHANGE = 'work.exchange';

    /** Рабочая очередь: отсюда читает consumer. */
    public const string WORK_QUEUE = 'work.queue';

    /**
     * Dead-letter exchange для рабочей очереди. Отвергнутые сообщения
     * (reject с requeue=false) уходят сюда, а отсюда - в retry_queue.
     */
    public const string WORK_DLX = 'work.dlx';

    /**
     * Retry-очередь с TTL. Сообщение ждёт здесь RETRY_TTL_MS,
     * после чего по своему DLX возвращаетсяобратно в work.queue.
     * Без такой паузы был бы busy-loop
     */
    public const string RETRY_QUEUE = 'work.retry.queue';

    /**
     * DLX retry-очереди: по истечении TTL сообщение уходит сюда потом обратно в work.
     */
    public const string RETRY_DLX = 'work.retry.dlx';

    /**
     * Parking lot - "парковка" для сообщений, исчерпавших попытки
     */
    public const string PARKING_LOT_QUEUE = 'work.parking_lot.queue';

    /**
     * Обмен для отправки в parking lot.
     */
    public const string PARKING_LOT_EXCHANGE = 'work.parking_lot.exchange';

    /**
     * Задержка перед повторной попыткой (мс).
     */
    public const int RETRY_TTL_MS = 5000;

    /**
     * Routing key для всех сообщений демо (default-стиль: один ключ).
     */
    public const string ROUTING_KEY = 'notification';

    /**
     * Объявляет всю топологию на переданном канале.
     * Идемпотентно при повторном вызове с теми же параметрами
     * (RabbitMQ не пересоздаёт уже существующие сущности с совпадающими свойствами)
     */
    public function declare(AMQPChannel $channel): void
    {
        $this->declareExchanges($channel);
        $this->declareQueues($channel);
        $this->declareBindings($channel);
    }

    private function declareExchanges(AMQPChannel $channel): void
    {
        foreach (
            [
                self::WORK_EXCHANGE,
                self::WORK_DLX,
                self::RETRY_DLX,
                self::PARKING_LOT_EXCHANGE,
            ] as $exchange
        ) {
            $channel->exchange_declare(
                exchange: $exchange,
                type: 'direct',
                durable: true,
                auto_delete: false
            );
        }
    }

    private function declareQueues(AMQPChannel $channel): void
    {
        /*
         * Отвергнутое (reject requeue=false) отправляется в work.dlx
         */
        $channel->queue_declare(
            queue: self::WORK_QUEUE,
            durable: true,
            auto_delete: false,
            arguments: new AMQPTable([
                'x-dead-letter-exchange' => self::WORK_DLX,
                'x-dead-letter-routing-key' => self::ROUTING_KEY,
            ])
        );

        $channel->queue_declare(
            queue: self::RETRY_QUEUE,
            durable: true,
            auto_delete: false,
            arguments: new AMQPTable([
                'x-message-ttl' => self::RETRY_TTL_MS,
                'x-dead-letter-exchange' => self::WORK_EXCHANGE,
                'x-dead-letter-routing-key' => self::ROUTING_KEY,
            ])
        );

        /*
         * Parking lot для ручного разбора - durable очередь без DLX
         */
        $channel->queue_declare(
            queue: self::PARKING_LOT_QUEUE,
            durable: true,
            auto_delete: false
        );
    }

    private function declareBindings(AMQPChannel $channel): void
    {
        $channel->queue_bind(
            queue: self::WORK_QUEUE,
            exchange: self::WORK_EXCHANGE,
            routing_key: self::ROUTING_KEY
        );

        $channel->queue_bind(
            queue: self::RETRY_QUEUE,
            exchange: self::WORK_DLX,
            routing_key: self::ROUTING_KEY
        );

        $channel->queue_bind(
            queue: self::WORK_QUEUE,
            exchange: self::RETRY_DLX,
            routing_key: self::ROUTING_KEY
        );


        $channel->queue_bind(
            queue: self::PARKING_LOT_QUEUE,
            exchange: self::PARKING_LOT_EXCHANGE,
            routing_key: self::ROUTING_KEY
        );
    }
}
