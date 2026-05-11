<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class TopBun extends Ingredient
{

    public function prepare(): string
    {
        return 'Положите верхушку сверху в конце';
    }
}
