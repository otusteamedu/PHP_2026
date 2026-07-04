<?php

namespace App\Application\Gateway\MessageBroker\Message;

interface MessageInterface
{
    public function getRoutingKey(): string;
}
