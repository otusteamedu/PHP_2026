<?php

declare(strict_types=1);

namespace App\Domain\Decorator;

use App\Domain\Entity\Cookable;

final readonly class OnionDecorator implements Cookable
{

    public function __construct(
        private Cookable $decorated
    )
    {
    }

    public function cook(): void
    {
        //Добавляем лук
        $this->decorated->cook();
    }
}
