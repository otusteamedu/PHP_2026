<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class Toast extends Ingredient
{

    public function prepare(): string
    {
        return 'Положите тост как основание или верхушку';
    }
}
