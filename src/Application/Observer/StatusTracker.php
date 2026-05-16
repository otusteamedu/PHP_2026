<?php

declare(strict_types=1);

namespace App\Application\Observer;

use App\Domain\Enum\Status;
use App\Domain\Observer\Event;
use App\Domain\Observer\ObserverInterface;
use App\Domain\Observer\StatusChanged;
use App\Domain\Observer\SubjectInterface;

class StatusTracker implements SubjectInterface
{
    /**
     * @param ObserverInterface[] $observers
     */
    public function __construct(
        private Status $status = Status::READY_TO_COOK,
        private array $observers = []
    )
    {
    }

    public function updateStatus(Status $status): void
    {
        $currentStatus = $this->status;
        $this->status = $status;
        $this->notify(new StatusChanged($currentStatus, $status));
    }

    public function attach(ObserverInterface $observer): void
    {
        $key = spl_object_hash($observer);
        $this->observers[$key] = $observer;
    }

    public function detach(ObserverInterface $observer): void
    {
        $key = spl_object_hash($observer);
        unset($this->observers[$key]);
    }

    public function notify(Event $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event);
        }
    }
}
