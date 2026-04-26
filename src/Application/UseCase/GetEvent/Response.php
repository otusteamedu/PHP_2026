<?php

declare(strict_types=1);

namespace App\Application\UseCase\GetEvent;

class Response
{
    public function __construct(
        public string $event,
    ) {
    }
}
