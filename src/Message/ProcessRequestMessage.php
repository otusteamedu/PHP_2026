<?php

namespace App\Message;

final readonly class ProcessRequestMessage
{
    public function __construct(public int $requestId)
    {
    }
}