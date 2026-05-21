<?php

namespace App\Observer\Publishers;

use App\Observer\Events\UserIsCreatedEvent;
use App\Observer\Interfaces\PublisherInterface;
use App\Observer\Interfaces\SubscriberInterface;

class Publisher implements PublisherInterface
{
    /** @var SubscriberInterface[] */
    private array $subscribers = [];

    public function subscribe(SubscriberInterface $subscriber): void
    {
        if (isset($this->subscribers[get_class($subscriber)])) return;
        $this->subscribers[get_class($subscriber)] = $subscriber;
    }

    public function unsubscribe(SubscriberInterface $subscriber): void
    {
        unset($this->subscribers[get_class($subscriber)]);
    }

    public function notify(UserIsCreatedEvent $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->handle($event);
        }
    }
}
