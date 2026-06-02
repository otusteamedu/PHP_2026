<?php

declare (strict_types = 1);

class BonusListener implements ListenerInterface
{
    public function handle(object $event): void
    {
        echo "Пользователю {$event->userId} начислено 100 бонусов.";
    }
}
