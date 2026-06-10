<?php

declare(strict_types=1);

namespace App\Core\Queue;

use RdKafka\Conf;
use RdKafka\KafkaConsumer as RdKafkaConsumer;
use RdKafka\Message;

/**
 * Reads messages from a Kafka topic (consumer side of the queue).
 */
final class KafkaConsumer
{
    private RdKafkaConsumer $consumer;

    public function __construct(private readonly QueueConfig $config)
    {
        $conf = new Conf();
        $conf->set('bootstrap.servers', $this->config->broker);
        $conf->set('group.id', $this->config->group);
        // Process messages produced before this consumer first connected.
        $conf->set('auto.offset.reset', 'earliest');

        $this->consumer = new RdKafkaConsumer($conf);
        $this->consumer->subscribe([$this->config->topic]);
    }

    /**
     * @param callable(string $payload): void $handler
     */
    public function consume(callable $handler): void
    {
        while (true) {
            $message = $this->consumer->consume(120_000);

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $handler($message->payload);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    // No new messages right now — keep waiting.
                    break;
                default:
                    fwrite(STDERR, '[kafka] error: ' . $message->errstr() . PHP_EOL);
                    break;
            }
        }
    }
}