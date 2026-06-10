<?php

declare(strict_types=1);

namespace App\Core\Queue;

use RdKafka\Conf;
use RdKafka\Producer;
use RuntimeException;

/**
 * Publishes messages to a Kafka topic (producer side of the queue).
 */
final class KafkaProducer
{
    private Producer $producer;

    public function __construct(private readonly QueueConfig $config)
    {
        $conf = new Conf();
        $conf->set('bootstrap.servers', $this->config->broker);
        // Fail fast instead of hanging the web request if the broker is unreachable.
        $conf->set('message.timeout.ms', '5000');

        $this->producer = new Producer($conf);
    }

    /**
     * Publish a payload and wait until it is acknowledged (or time out).
     */
    public function publish(string $payload, ?string $key = null): void
    {
        $topic = $this->producer->newTopic($this->config->topic);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $payload, $key);
        $this->producer->poll(0);

        $result = $this->producer->flush(10_000);

        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            throw new RuntimeException('Unable to publish message to Kafka.');
        }
    }
}