<?php

namespace App\Interfaces;

use App\Models\Event;

interface StorageInterface
{
    public function addEvent(Event $event): void;

    public function clearEvents(): void;

    public function findBestMatchingEvent(array $params): ?Event;
}
