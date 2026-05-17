<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class HotDog implements Cookable
{
    public function getName(): string
    {
        return 'Хот Дог';
    }

    public function prepare(): string
    {
        return 'булка + сосиска';
    }
}
