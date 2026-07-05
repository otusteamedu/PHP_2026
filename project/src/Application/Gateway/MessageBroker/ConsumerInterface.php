<?php

namespace App\Application\Gateway\MessageBroker;

interface ConsumerInterface
{
    public function consume(): void;
}
