<?php

namespace App\Repositores;

use App\Interfaces\StorageInterface;
use App\Models\Event;

class EventRepository
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function addEvent(Event $event): void
    {
        $this->storage->addEvent($event);
    }

    public function clearEvents(): void
    {
        $this->storage->clearEvents();
    }

    public function findBestEvent(array $params): ?Event
    {
        return $this->storage->findBestMatchingEvent($params);
    }
}