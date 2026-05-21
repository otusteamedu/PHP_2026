<?php

namespace App\Observer\Subscribers;

use App\Observer\Events\UserIsCreatedEvent;
use App\Observer\Interfaces\SubscriberInterface;

class Logger implements SubscriberInterface
{
    public function handle(UserIsCreatedEvent $event): void
    {
        echo "Пишем в лог данные: пользователь {$event->user->getFirstName()} {$event->user->getLastName()} зарегистрирован" . PHP_EOL;
    }
}
