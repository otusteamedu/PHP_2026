<?php
declare(strict_types=1);

namespace App\Services;

use App\Domain\Event;
use App\Infrastructure\EventServiceInterface;

readonly class EventMatcherService
{
    public function __construct(
        private EventServiceInterface $storage
    ) {}

    public function findEvent(array $params): ?Event
    {       
      
        $eventIds = $this->storage->find($params);

        foreach ($eventIds as $id) {           
            $event = $this->storage->getById($id);
                       
            if ($event !== null && $event->matches($params)) {
                return $event;
            }
        }

        return null;
    }
}