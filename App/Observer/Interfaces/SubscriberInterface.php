<?php

namespace App\Observer\Interfaces;

use App\Observer\Events\UserIsCreatedEvent;

interface SubscriberInterface
{
    public function handle(UserIsCreatedEvent $event): void;
}