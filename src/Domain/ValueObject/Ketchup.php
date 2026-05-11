<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class Ketchup extends Ingredient
{

    public function prepare(): string
    {
        return 'Добавьте кетчуп';
    }
}
