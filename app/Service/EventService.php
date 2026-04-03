<?php

declare(strict_types=1);

namespace App\Service;

use App\Storage\StorageInterface;

final readonly class EventService
{
    public function __construct(private StorageInterface $storage) {}

    public function add(int $priority, array $conditions, array $event): void
    {
        $this->storage->addEvent($priority, $conditions, $event);
    }

    public function clear(): void
    {
        $this->storage->clearEvents();
    }

    public function match(array $params): ?array
    {
        return $this->storage->findBestMatch($params);
    }
}
