<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Burger implements Cookable
{
    public function getName(): string
    {
        return 'Бургер';
    }

    public function prepare(): string
    {
        return 'булка + кетчуп + котлета';
    }
}
