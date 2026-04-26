<?php

declare(strict_types=1);

namespace App\Application\UseCase\GetEvent;

class Request
{
    /**
     * @param array<string, int> $conditions
     */
    public function __construct(
        public array $conditions,
    ) {
    }
}
