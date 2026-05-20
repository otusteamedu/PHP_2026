<?php

declare(strict_types=1);

namespace App\Observer;

use App\Domain\Product\ProductInterface;
use App\Domain\Status\CookingStatus;

final class ClientNotifier implements CookingObserverInterface
{
    public function onStatusChange(ProductInterface $product, CookingStatus $status): void
    {
        echo sprintf(
            "[SMS клиенту] «%s» — %s\n",
            $product->getName(),
            $status->label(),
        );
    }
}