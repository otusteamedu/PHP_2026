<?php

declare(strict_types=1);

namespace App\Infrastructure\MessageBroker\Worker;

use App\Application\Gateway\MessageBroker\ConsumerInterface;
use App\Application\Gateway\MessageBroker\Message\MessageDispatcher;
use App\Application\Gateway\MessageBroker\Message\MessageInterface;
use App\Infrastructure\MessageBroker\Amqp\Connection;
use App\Infrastructure\MessageBroker\Amqp\DeathCounter;
use App\Infrastructure\MessageBroker\Amqp\Topology;
use PhpAmqpLib\Message\AMQPMessage;

final readonly class Consumer implements ConsumerInterface
{
    public function __construct(
        private Connection $connection,
        private Topology $topology,
        private MessageDispatcher $dispatcher,
    ) {
    }

    public function consume(): void
    {
        $channel = $this->connection->channel();
        $this->topology->declare($channel);

        /*
         * prefetch=1: брокер не выдаёт следующее сообщение, пока не получил
         * ack/reject по-текущему. Для предсказуемости при ручном ack
         */
        $channel->basic_qos(prefetch_size: 0, prefetch_count: 1, a_global: false);

        $channel->basic_consume(
            queue: Topology::WORK_QUEUE,
            callback: fn(AMQPMessage $message) => $this->handle($message)
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    private function handle(AMQPMessage $message): void
    {
        $data = unserialize($message->getBody()) ?? [];
        if (!$data instanceof MessageInterface) {
            //упрощенно просто не работаем, если сообщение неправильного формата.
            $message->ack();
            return;
        }

        $attempts = DeathCounter::attempts($message);

        if ($attempts >= Topology::MAX_ATTEMPTS) {
            // Лимит исчерпан, отправляем в parking lot без новой обработки.
            $this->parkMessage($message);
            return;
        }

        try {
            $this->dispatcher->dispatch($data);
            $message->ack();
        } catch (\RuntimeException $e) {
            // Сбой — reject без requeue: сообщение уходит в work.dlx, тогда retry_queue,
            // отлёживается RETRY_TTL_MS и возвращается в work.queue.
            $message->reject(requeue: false);
        }
    }

    /**
     * Отправляет исчерпавшее попытки сообщение в parking lot и подтверждает
     * оригинал (ack), чтобы оно ушло из рабочего цикла окончательно.
     */
    private function parkMessage(AMQPMessage $message): void
    {
        $channel = $this->connection->channel();

        /*
         * Публикуем тело в parking lot exchange. Сохраняем заголовки для истории
         */
        $parked = new AMQPMessage(
            body: $message->getBody(),
            properties: [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => $message->has('application_headers')
                    ? $message->get('application_headers')
                    : null,
            ]
        );

        $channel->basic_publish(
            msg: $parked,
            exchange: Topology::PARKING_LOT_EXCHANGE,
            routing_key: Topology::ROUTING_KEY
        );

        $message->ack();
    }
}
