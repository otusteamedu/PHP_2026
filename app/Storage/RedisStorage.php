<?php

declare(strict_types=1);

namespace App\Storage;

use Redis;

final class RedisStorage implements StorageInterface
{
    use ConditionsTrait;

    public function __construct(private readonly Redis $redis) {}

    /**
     * @throws \JsonException
     */
    public function addEvent(int $priority, array $conditions, array $event): void
    {
        $this->redis->zAdd(
            'events',
            $priority,
            json_encode([
                'id' => uniqid('', true),
                'conditions' => $conditions,
                'event' => $event,
            ], JSON_THROW_ON_ERROR),
        );
    }

    public function clearEvents(): void
    {
        $this->redis->del('events');
    }

    /**
     * @throws \JsonException
     */
    public function findBestMatch(array $params): ?array
    {
        foreach ($this->redis->zRevRange('events', 0, -1) as $raw) {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

            if ($this->matches($data['conditions'], $params)) {
                return $data['event'];
            }
        }
        return null;
    }
}
