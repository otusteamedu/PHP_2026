<?php

namespace App\Domain\Configurator;

use App\Domain\Entity\Cookable;

interface ProductConfiguratorInterface
{
    public function configure(Cookable $product): Cookable;
}
