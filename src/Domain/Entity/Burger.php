<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\BottomBun;
use App\Domain\ValueObject\Cutlet;
use App\Domain\ValueObject\TopBun;

final class Burger extends Food
{
    protected function defaultIngredients(): array
    {
        return [
            new BottomBun(),
            new Cutlet(2),
            new TopBun(100),
        ];
    }
}
