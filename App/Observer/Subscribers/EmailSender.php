<?php

namespace App\Observer\Subscribers;

use App\Observer\Events\UserIsCreatedEvent;
use App\Observer\Interfaces\SubscriberInterface;

class EmailSender implements SubscriberInterface
{
    public function handle(UserIsCreatedEvent $event): void
    {
        echo "Отправляем на почту {$event->user->getEmail()} уведомление" . PHP_EOL;
    }
}