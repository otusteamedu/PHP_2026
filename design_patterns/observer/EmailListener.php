<?php

declare (strict_types = 1);

class EmailListener implements ListenerInterface
{
    public function handle(object $event): void
    {
        echo "Отправлено приветственное письмо на {$event->email}";
    }
}
