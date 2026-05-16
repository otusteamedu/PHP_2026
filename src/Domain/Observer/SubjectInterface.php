<?php

namespace App\Domain\Observer;

interface SubjectInterface
{
    public function attach(ObserverInterface $observer): void;
    public function detach(ObserverInterface $observer): void;

    public function notify(Event $event): void;
}
