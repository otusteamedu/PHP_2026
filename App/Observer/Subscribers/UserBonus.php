<?php

namespace App\Observer\Subscribers;

use App\Observer\Events\UserIsCreatedEvent;
use App\Observer\Interfaces\SubscriberInterface;

class UserBonus implements SubscriberInterface
{
    public function handle(UserIsCreatedEvent $event): void
    {
        echo "Добавляем 100 бонусов пользователю {$event->user->getFirstName()} {$event->user->getLastName()} за регистрацию" . PHP_EOL;
    }
}
