<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Burger implements Cookable
{

    public function prepare(): string
    {
        return 'Mug + Ketchup + Cutlet';
    }
}
