<?php

namespace App\Application\Gateway\MessageBroker\Message;

interface MessageHandlerInterface
{
    public function handle(MessageInterface $message): void;
}
