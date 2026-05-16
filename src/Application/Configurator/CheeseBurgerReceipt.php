<?php

declare(strict_types=1);

namespace App\Application\Configurator;

use App\Domain\Configurator\ProductConfiguratorInterface;
use App\Domain\Decorator\CheeseDecorator;
use App\Domain\Entity\Cookable;

class CheeseBurgerReceipt implements ProductConfiguratorInterface
{
    public function configure(Cookable $product): Cookable
    {
        return new CheeseDecorator($product);
    }
}
