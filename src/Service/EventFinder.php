<?php
declare(strict_types=1);

namespace Evgeny87\RedisLab\Service;

use Evgeny87\RedisLab\Contract\EventRepositoryInterface;

/**
 * Сервис для поиска и фильтрации наиболее подходящего события.
 */
final readonly class EventFinder
{
    public function __construct(
        private EventRepositoryInterface $repository
    ) {}

    /**
     * Возвращает полезную нагрузку (payload) события с наивысшим приоритетом.
     */
    public function findBest(array $criteria): ?array
    {
        // Если критерии не заданы, возвращать нечего
        if (empty($criteria)) {
            return null;
        }

        // Получаем всех кандидатов из хранилища (через интерфейс)
        $candidates = $this->repository->findCandidates($criteria);

        if (empty($candidates)) {
            return null;
        }

        /**
         * Сортируем кандидатов по убыванию приоритета (priority DESC).
         * Используем современный оператор <=> (spaceship operator).
         */
        usort($candidates, static fn(array $a, array $b): int => $b['priority'] <=> $a['priority']);

        // Возвращаем данные самого первого (самого приоритетного) события
        return $candidates[0]['payload'] ?? null;
    }
}
