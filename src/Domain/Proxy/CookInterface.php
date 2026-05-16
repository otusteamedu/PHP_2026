<?php

namespace App\Domain\Proxy;

use App\Domain\Entity\Cookable;

interface CookInterface
{
    public function cook(Cookable $product): void;
}
