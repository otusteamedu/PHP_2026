<?php

namespace App\Storages;

use App\Interfaces\StorageInterface;
use App\Models\Event;
use Redis;

class RedisStorage implements StorageInterface
{

    private Redis $redis;
    private string $keyPrefix = 'events:';

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('redis', 6379);
    }

    public function addEvent(Event $event): void
    {
        $eventId = uniqid();
        $data = $event->toArray();

        $this->redis->hSet(
            $this->keyPrefix . $eventId,
            'data',
            json_encode($data)
        );
        $this->redis->zAdd(
            $this->keyPrefix . 'priorities',
            $event->priority,
            $eventId
        );
    }

    public function clearEvents(): void
    {
        $keys = $this->redis->keys($this->keyPrefix . '*');
        foreach ($keys as $key) {
            $this->redis->del($key);
        }
    }

    public function findBestMatchingEvent(array $params): ?Event
    {
        $allEventIds = $this->redis->zRange(
            $this->keyPrefix . 'priorities',
            0,
            -1,
            true
        );

        krsort($allEventIds);

        foreach ($allEventIds as $eventId => $priority) {
            $eventData = json_decode(
                $this->redis->hGet($this->keyPrefix . $eventId, 'data'),
                true
            );

            if ($this->conditionsMatch($eventData['conditions'], $params)) {
                return new Event(
                    $priority,
                    $eventData['conditions'],
                    $eventData['event']
                );
            }
        }

        return null;
    }

    private function conditionsMatch(array $conditions, array $params): bool
    {
        foreach ($conditions as $key => $value) {
            if (!isset($params[$key]) || $params[$key] < $value) {
                return false;
            }
        }
        return true;
    }
}
