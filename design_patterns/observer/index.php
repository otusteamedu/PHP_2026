<?php

class UserRegistered
{  
    public function __construct(
        public string $userId,
        public string $email
    ) {}
}


$action = new EventDispatcher();

$action->listen(UserRegistered::class, new EmailListener());
$action->listen(UserRegistered::class, new BonusListener());
$action->listen(UserRegistered::class, new LogListener());

$action->dispatch(new UserRegistered(1, 'example@mail.ru'));