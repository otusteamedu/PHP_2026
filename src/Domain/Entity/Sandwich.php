<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final readonly class Sandwich implements Cookable
{
    public function getName(): string
    {
        return 'Сэндвич';
    }

    public function prepare(): string
    {
        return 'тосты + сыр';
    }
}
