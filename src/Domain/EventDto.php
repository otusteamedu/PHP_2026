<?php
declare(strict_types=1);

namespace Evgeny87\RedisLab\Domain;

/**
 * Data Transfer Object для события.
 * Инкапсулирует данные события, защищая их от изменений.
 */
final readonly class EventDto
{
    /**
     * @param int $priority Важность события (целое число)
     * @param array $conditions Критерии возникновения (param => value)
     * @param array $payload Данные самого события (::event::)
     */
    public function __construct(
        public int $priority,
        public array $conditions,
        public array $payload
    ) {}
}
