<?php

declare(strict_types=1);

namespace App\Storage;

interface StorageInterface
{
    public function addEvent(int $priority, array $conditions, array $event): void;
    public function clearEvents(): void;
    public function findBestMatch(array $params): ?array;
}