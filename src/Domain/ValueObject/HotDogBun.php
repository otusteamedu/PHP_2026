<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class HotDogBun extends Ingredient
{

    public function prepare(): string
    {
        return 'Возьмите булку для хот дога';
    }
}
