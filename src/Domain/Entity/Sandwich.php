<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Sandwich implements Cookable
{

    public function prepare(): string
    {
        return 'Toasts + cheese';
    }
}
