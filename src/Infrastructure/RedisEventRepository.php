<?php
declare(strict_types=1);

namespace Evgeny87\RedisLab\Infrastructure;

use Evgeny87\RedisLab\Contract\EventRepositoryInterface;
use Evgeny87\RedisLab\Domain\EventDto;
use Redis;
use Exception;

final class RedisEventRepository implements EventRepositoryInterface
{
    private const string PREFIX_EVENT = 'ev:';
    private const string PREFIX_IDX   = 'idx:';
    private const string REGISTRY     = 'events_registry';

    public function __construct(private Redis $redis) {}

    private function makeIndexKey(string $key, string $value): string
    {
        // MD5 от пары ключ:значение — безопасно и фиксированной длины
        return self::PREFIX_IDX . md5($key . ':' . $value);
    }

    public function store(EventDto $event): string
    {
        $id = bin2hex(random_bytes(8));

        $this->redis->multi();

        // Сохраняем данные события
        $this->redis->hMSet(self::PREFIX_EVENT . $id, [
            'priority'   => (string)$event->priority,
            'conditions' => json_encode($event->conditions),
            'payload'    => json_encode($event->payload)
        ]);

        // Индексируем каждое условие
        foreach ($event->conditions as $k => $v) {
            $this->redis->sAdd($this->makeIndexKey($k, (string)$v), $id);
        }

        // Добавляем ID в общий реестр для очистки
        $this->redis->sAdd(self::REGISTRY, $id);

        $this->redis->exec();

        return $id;
    }

    public function findCandidates(array $criteria): array
    {
        if (empty($criteria)) {
            return [];
        }

        $indexKeys = [];
        foreach ($criteria as $k => $v) {
            $indexKeys[] = $this->makeIndexKey($k, (string)$v);
        }

        try {
            $ids = $this->redis->sUnion(...$indexKeys);
            if (empty($ids)) {
                return [];
            }

            $results = [];
            foreach ($ids as $id) {
                $data = $this->redis->hGetAll(self::PREFIX_EVENT . $id);
                if (!$data) {
                    continue;
                }

                $conditions = json_decode($data['conditions'] ?? '{}', true);

                // Проверяем, что все условия события присутствуют в запросе
                $match = true;
                foreach ($conditions as $k => $v) {
                    if (!isset($criteria[$k]) || $criteria[$k] != $v) {
                        $match = false;
                        break;
                    }
                }

                if ($match) {
                    $results[] = [
                        'priority' => (int)$data['priority'],
                        'payload'  => json_decode($data['payload'], true)
                    ];
                }
            }
            return $results;
        } catch (Exception $e) {
            // Логирование ошибки (для учебного проекта просто возвращаем пустой массив)
            return [];
        }
    }

    public function clearAll(): void
    {
        // Получаем все ID событий из реестра
        $ids = $this->redis->sMembers(self::REGISTRY);

        $this->redis->multi();

        // Удаляем данные каждого события
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->redis->del(self::PREFIX_EVENT . $id);
            }
        }

        // Удаляем все индексные ключи
        $indexKeys = $this->redis->keys(self::PREFIX_IDX . '*');
        if (is_array($indexKeys) && !empty($indexKeys)) {
            $this->redis->del(...$indexKeys);
        }

        // Удаляем реестр
        $this->redis->del(self::REGISTRY);

        $this->redis->exec();
    }
}
