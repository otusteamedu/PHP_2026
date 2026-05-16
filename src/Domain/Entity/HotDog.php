<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class HotDog implements Cookable
{

    public function prepare(): string
    {
        return 'Bread + sausage';
    }
}
