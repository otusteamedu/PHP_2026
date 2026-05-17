<?php

namespace App\Domain\Observer;

use App\Domain\Observer\Event\Event;

interface SubjectInterface
{
    /**
     * @param ObserverInterface<Event> $observer
     */
    public function attach(ObserverInterface $observer): void;

    /**
     * @param ObserverInterface<Event> $observer
     */
    public function detach(ObserverInterface $observer): void;

    public function notify(Event $event): void;
}
