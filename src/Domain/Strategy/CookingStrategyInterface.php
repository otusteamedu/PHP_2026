<?php

namespace App\Domain\Strategy;

use App\Domain\Factory\CreateProductInterface;

interface CookingStrategyInterface
{
    public function getFactory(): CreateProductInterface;
}
