<?php

declare (strict_types = 1);

class LogListener implements ListenerInterface
{
    public function handle(object $event): void
    {
        echo "Пользователь {$event->userId} успешно зарегистрирован.";
    }
}
