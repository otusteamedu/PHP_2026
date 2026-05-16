<?php

namespace App\Domain\Observer;

interface ObserverInterface
{
    public function update(Event $event): void;
}
