<?php
declare(strict_types=1);

namespace Evgeny87\RedisLab\Infrastructure;

use Evgeny87\RedisLab\Contract\EventRepositoryInterface;
use Evgeny87\RedisLab\Domain\EventDto;
use Redis;
use RedisException;
use JsonException;
use RuntimeException;
use Exception;

final readonly class RedisEventRepository implements EventRepositoryInterface
{
    private const string PREFIX_EVENT = 'ev:';
    private const string PREFIX_IDX = 'idx:';
    private const string REGISTRY_KEY = 'events_registry';

    public function __construct(private Redis $redis) {}

    public function store(EventDto $event): string 
    {
        try {
            $id = bin2hex(random_bytes(8));
            $eventKey = self::PREFIX_EVENT . $id;

            $this->redis->multi();

            $this->redis->hMSet($eventKey, [
                'priority'   => (string)$event->priority,
                'conditions' => json_encode($event->conditions, JSON_THROW_ON_ERROR),
                'payload'    => json_encode($event->payload, JSON_THROW_ON_ERROR)
            ]);

            foreach ($event->conditions as $key => $value) {
                $this->redis->sAdd(self::PREFIX_IDX . "{$key}:{$value}", $id);
            }

            $this->redis->sAdd(self::REGISTRY_KEY, $id);
            $this->redis->exec();
            
            return $id;
        } catch (RedisException | JsonException $e) {
            $this->redis->discard();
            throw new RuntimeException("Storage Error: " . $e->getMessage());
        }
    }

    public function findCandidates(array $criteria): array 
    {
        if (empty($criteria)) {
            return [];
        }

        $indexKeys = array_map(
            fn($k, $v) => self::PREFIX_IDX . "{$k}:{$v}", 
            array_keys($criteria), 
            $criteria
        );

        try {
            // Используем sUnion, чтобы найти ВСЕ события, где совпал хотя бы один критерий
            $ids = $this->redis->sUnion(...$indexKeys);
            
            if (empty($ids)) {
                return [];
            }

            $results = [];
            foreach ($ids as $id) {
                $data = $this->redis->hGetAll(self::PREFIX_EVENT . $id);
                if (!$data) continue;

                $eventConditions = json_decode($data['conditions'], true, 512, JSON_THROW_ON_ERROR);
                
                foreach ($eventConditions as $key => $val) {
                    if (!isset($criteria[$key]) || $criteria[$key] != $val) {
                        // Если хотя бы одно условие события НЕ совпало с запросом — пропускаем
                        continue 2; 
                    }
                }

                $results[] = [
                    'priority' => (int)$data['priority'],
                    'payload'  => json_decode($data['payload'], true, 512, JSON_THROW_ON_ERROR)
                ];
            }
            return $results;

        } catch (Exception $e) {
            error_log("Search Candidates Error: " . $e->getMessage());
            return [];
        }
    }

    public function clearAll(): void 
    {
        try {
            $ids = $this->redis->sMembers(self::REGISTRY_KEY);
            if (!empty($ids)) {
                $this->redis->multi();
                foreach ($ids as $id) {
                    $this->redis->del(self::PREFIX_EVENT . $id);
                }
                $this->redis->del(self::REGISTRY_KEY);
                $this->redis->exec();
            }

            $indexKeys = $this->redis->keys(self::PREFIX_IDX . '*');
            if (!empty($indexKeys)) {
                $this->redis->del(...$indexKeys);
            }
        } catch (RedisException $e) {
            error_log("Clear Storage Error: " . $e->getMessage());
        }
    }
}
