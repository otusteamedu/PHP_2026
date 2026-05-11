<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\HotDogBun;
use App\Domain\ValueObject\Sausage;

final class HotDog extends Food
{
    protected function defaultIngredients(): array
    {
        return [
            new HotDogBun(),
            new Sausage(2),
        ];
    }
}
