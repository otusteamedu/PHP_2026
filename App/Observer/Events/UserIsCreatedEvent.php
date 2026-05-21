<?php

namespace App\Observer\Events;

use App\Observer\Entitys\User;

class UserIsCreatedEvent
{
    public function __construct(public readonly User $user) {}
}