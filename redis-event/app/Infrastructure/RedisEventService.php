<?php

declare (strict_types = 1);

namespace App\Infrastructure;

use App\Domain\Event;
use Redis;

final readonly class RedisEventService implements EventServiceInterface
{
    private const KEY_PRIORITY     = 'idx:priority';
    private const PREFIX_EVENT     = 'event:';
    private const PREFIX_CONDITION = 'condition:';

    private const KEY_EMPTY_CONDITIONS = 'idx:empty_conditions';

    public function __construct(
        private readonly Redis $redis
    ) {}

    public function add(Event $event): void
    {
        //Получили ключ 'event:12345'
        $eventKey = $this->eventKey($event->id);

        $this->removeOldIndexes($event->id);

        $conditionsJson = json_encode($event->conditions, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $eventDataJson  = json_encode($event->eventData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        //Транзакция Redis
        $this->redis->multi();

        //Записываем в Redis стоку ключ-значение
        $this->redis->hMset($eventKey, [
            'id'         => $event->id,
            'priority'   => (string) $event->priority,
            'conditions' => $conditionsJson,
            'eventData'  => $eventDataJson,
        ]);

        //Создаем запись в таблицу
        //Индекс приоритетов
        $this->redis->zAdd(self::KEY_PRIORITY, $event->priority, $event->id);
        // индексы условий (SET)
        if (empty($event->conditions)) {
            $this->redis->sAdd(self::KEY_EMPTY_CONDITIONS, $event->id);
        } else {
            foreach ($event->conditions as $key => $val) {
                $key = rawurlencode($key);
                $val = rawurlencode((string) $val);

                $this->redis->sAdd(self::PREFIX_CONDITION . "$key:$val", $event->id);
            }
        }

        $this->redis->exec();
    }

    public function clear(): void
    {
        $ids = $this->redis->zRange(self::KEY_PRIORITY, 0, -1);

        foreach ($ids as $id) {
            $id        = (string) $id;
            $eventKey  = $this->eventKey($id);
            $indexData = $this->redis->hGetAll($eventKey);

            if (isset($indexData['conditions'])) {
                try {
                    $conditions = $this->decodeArray((string) $indexData['conditions']);
                } catch (\JsonException) {
                    $conditions = [];
                }

                foreach ($conditions as $key => $value) {
                    if (! is_string($key) || $key === '') {
                        continue;
                    }

                    $this->redis->sRem($this->conditionKey($key, (string) $value), $id);
                }
            }

            $this->redis->del($eventKey);
        }

        $this->redis->del(self::KEY_PRIORITY, self::KEY_EMPTY_CONDITIONS);
    }

    public function find(array $params): array
    {
        $ids = [];

        foreach ($this->redis->sMembers(self::KEY_EMPTY_CONDITIONS) as $id) {
            $ids[(string) $id] = true;
        }

        foreach ($params as $key => $value) {
            if (! is_string($key) || $key === '') {
                continue;
            }

            $conditionKey = $this->conditionKey($key, (string) $value);

            foreach ($this->redis->sMembers($conditionKey) as $id) {
                $ids[(string) $id] = true;
            }
        }

        if ($ids === []) {
            return [];
        }

        $sortedIds = [];

        foreach ($this->redis->zRevRange(self::KEY_PRIORITY, 0, -1) as $id) {
            $id = (string) $id;

            if (isset($ids[$id])) {
                $sortedIds[] = $id;
            }
        }

        return $sortedIds;
    }

    public function getById(string $id): ?Event
    {
        $data = $this->redis->hGetAll($this->eventKey($id));

        if ($data === []) {
            return null;
        }

        if (! isset($data['id'], $data['priority'], $data['conditions'], $data['eventData'])) {
            return null;
        }

        try {
            $conditions = $this->decodeArray((string) $data['conditions']);
            $eventData  = $this->decodeArray((string) $data['eventData']);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Invalid JSON stored in Redis.', 0, $e);
        }

        return Event::getEventfromStorage([
            'id'         => (string) $data['id'],
            'priority'   => (int) $data['priority'],
            'conditions' => $conditions,
            'eventData'  => $eventData,
        ]);
    }

    private function eventKey(string $id): string
    {
        return self::PREFIX_EVENT . $id;
    }

    private function removeOldIndexes(string $id): void
    {
        $eventKey  = $this->eventKey($id);
        $indexData = $this->redis->hGetAll($eventKey);

        if ($indexData === []) {
            return;
        }

        $this->redis->zRem(self::KEY_PRIORITY, $id);
        $this->redis->sRem(self::KEY_EMPTY_CONDITIONS, $id);

        if (isset($indexData['conditions'])) {
            try {
                $conditions = $this->decodeArray((string) $indexData['conditions']);
            } catch (\JsonException) {
                $conditions = [];
            }

            foreach ($conditions as $key => $value) {
                if (! is_string($key) || $key === '') {
                    continue;
                }

                $this->redis->sRem($this->conditionKey($key, (string) $value), $id);
            }
        }

        $this->redis->del($eventKey);
    }

    private function decodeArray(string $value): array
    {
        $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    private function conditionKey(string $key, string $value): string
    {
        return self::PREFIX_CONDITION . rawurlencode($key) . ':' . rawurlencode($value);
    }
}
