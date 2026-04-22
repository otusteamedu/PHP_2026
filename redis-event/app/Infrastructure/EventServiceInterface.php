<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Event;

interface EventServiceInterface
{
    public function add(Event $event): void;
    
    public function clear(): void;    

    public function find(array $params): array;

     public function getById(string $id): ?Event;
}