<?php
declare(strict_types=1);

namespace Evgeny87\RedisLab\Contract;

use Evgeny87\RedisLab\Domain\EventDto;

/**
 * Интерфейс хранилища. 
 * Позволяет легко заменить Redis на другое NoSQL решение.
 */
interface EventRepositoryInterface
{
    public function store(EventDto $event): string;
    public function findCandidates(array $criteria): array;
    public function clearAll(): void;
}
