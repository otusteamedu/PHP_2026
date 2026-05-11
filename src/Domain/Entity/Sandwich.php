<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Cheese;
use App\Domain\ValueObject\Toast;

final class Sandwich extends Food
{
    protected function defaultIngredients(): array
    {
        return [
            new Toast(),
            new Cheese(2),
            new Toast(100),
        ];
    }
}
