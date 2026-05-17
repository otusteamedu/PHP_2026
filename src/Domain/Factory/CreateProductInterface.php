<?php

namespace App\Domain\Factory;

use App\Domain\Entity\Cookable;

interface CreateProductInterface
{
    public function create(): Cookable;
}
