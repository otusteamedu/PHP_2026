<?php
declare(strict_types=1);

namespace Evgeny87\RedisLab\Infrastructure;

use Evgeny87\RedisLab\Contract\EventRepositoryInterface;
use Evgeny87\RedisLab\Domain\EventDto;

/**
 * Вторая реализация: Хранилище в памяти (Array).
 * Позволяет работать без Redis (например, для Unit-тестов).
 */
final class InMemoryEventRepository implements EventRepositoryInterface
{
    private array $storage = [];

    public function store(EventDto $event): string
    {
        $id = bin2hex(random_bytes(8));
        $this->storage[$id] = [
            'priority' => $event->priority,
            'conditions' => $event->conditions,
            'payload' => $event->payload
        ];
        return $id;
    }

    public function findCandidates(array $criteria): array
    {
        $results = [];
        foreach ($this->storage as $event) {
            // Проверяем, что ВСЕ условия события есть в запросе пользователя
            $match = true;
            foreach ($event['conditions'] as $key => $value) {
                if (!isset($criteria[$key]) || $criteria[$key] != $value) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $results[] = [
                    'priority' => $event['priority'],
                    'payload'  => $event['payload']
                ];
            }
        }
        return $results;
    }

    public function clearAll(): void
    {
        $this->storage = [];
    }
}
