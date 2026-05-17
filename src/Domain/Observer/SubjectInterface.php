<?php

namespace App\Domain\Observer;

use App\Domain\Observer\Event\Event;

interface SubjectInterface
{
    public function attach(ObserverInterface $observer): void;
    public function detach(ObserverInterface $observer): void;

    public function notify(Event $event): void;
}
