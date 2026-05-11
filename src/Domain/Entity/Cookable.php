<?php

declare(strict_types=1);

namespace App\Domain\Entity;

interface Cookable
{
    public function cook(): array;
}
