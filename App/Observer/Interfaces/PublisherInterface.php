<?php

namespace App\Observer\Interfaces;

use App\Observer\Events\UserIsCreatedEvent;

interface PublisherInterface
{
    public function subscribe(SubscriberInterface $subscriber): void;

    public function unsubscribe(SubscriberInterface $subscriber): void;

    public function notify(UserIsCreatedEvent $event): void;
}
