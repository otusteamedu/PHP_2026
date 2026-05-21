<?php

namespace App\Observer\UseCase;

use App\Observer\Entitys\User;
use App\Observer\Events\UserIsCreatedEvent;
use App\Observer\Interfaces\PublisherInterface;

class CreateUserUseCase
{
    public function __construct(private readonly PublisherInterface $publisher) {}
    public function __invoke(): void
    {
        $user = new User('Вася', 'Васильев', 'example@example.tu');
        $event = new UserIsCreatedEvent($user);
        $this->publisher->notify($event);
    }
}