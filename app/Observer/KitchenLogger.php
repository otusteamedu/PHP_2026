<?php

declare(strict_types=1);

namespace App\Observer;

use App\Domain\Product\ProductInterface;
use App\Domain\Status\CookingStatus;

final class KitchenLogger implements CookingObserverInterface
{
    public function onStatusChange(ProductInterface $product, CookingStatus $status): void
    {
        error_log(sprintf(
            '[kitchen][%s] %s -> %s',
            date('H:i:s'),
            $product->getName(),
            $status->value,
        ));
    }
}