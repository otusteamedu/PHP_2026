<?php

namespace App\Application\Gateway\Notification;

interface NotifierInterface
{
    public function notify(Data $data): void;
}
