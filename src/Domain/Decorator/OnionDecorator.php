<?php

declare(strict_types=1);

namespace App\Domain\Decorator;

use App\Domain\Entity\Cookable;
use App\Domain\ValueObject\Onion;

final readonly class OnionDecorator implements Cookable
{

    public function __construct(
        private Cookable $decorated
    )
    {
    }

    public function cook(): array
    {
        $result = $this->decorated->cook();
        $result[] = new Onion(3);

        return $result;
    }
}
