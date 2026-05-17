<?php

declare(strict_types=1);

namespace App\Application\Observer;

use App\Application\Observer\Event\StatusChanged;
use App\Domain\Enum\Status;
use App\Domain\Observer\Event\Event;
use App\Domain\Observer\ObserverInterface;
use App\Domain\Observer\SubjectInterface;

class StatusTracker implements SubjectInterface
{
    /**
     * @param array<string, ObserverInterface<Event>> $observers
     */
    public function __construct(
        private string $product = '',
        private Status $status = Status::READY_TO_COOK,
        private array $observers = []
    ) {
    }

    public function init(string $product): void
    {
        $this->status = Status::READY_TO_COOK;
        $this->product = $product;
    }

    public function updateStatus(Status $status): void
    {
        $currentStatus = $this->status;
        $this->status = $status;
        $this->notify(new StatusChanged($this->product, $currentStatus, $status));
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
