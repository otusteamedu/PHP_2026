<?php

declare(strict_types=1);

namespace App\Domain\Decorator;

use App\Domain\Entity\Cookable;

abstract readonly class Decorator implements Cookable
{
    public function __construct(
        private Cookable $decorated
    )
    {
    }

    public function prepare(): string
    {
        return $this->decorated->prepare();
    }
}
