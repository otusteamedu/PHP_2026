<?php

declare(strict_types=1);

namespace App\Core\Queue;

final readonly class QueueConfig
{
    public function __construct(
        public string $broker,
        public string $topic,
        public string $group,
    ) {
    }

    public static function fromEnv(): self
    {
        return new self(
            broker: getenv('KAFKA_BROKER') ?: 'kafka:9092',
            topic:  getenv('KAFKA_TOPIC') ?: 'statement_requests',
            group:  getenv('KAFKA_GROUP') ?: 'statement_workers',
        );
    }
}
