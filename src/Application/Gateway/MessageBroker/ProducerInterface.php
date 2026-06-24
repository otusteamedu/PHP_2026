<?php

namespace App\Application\Gateway\MessageBroker;

use App\Application\Gateway\MessageBroker\Message\MessageInterface;

interface ProducerInterface
{
    public function publish(MessageInterface $message): void;
}
