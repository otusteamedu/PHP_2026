<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class BottomBun extends Ingredient
{

    public function prepare(): string
    {
        return 'Используйте нижнюю часть булки как основание';
    }
}
