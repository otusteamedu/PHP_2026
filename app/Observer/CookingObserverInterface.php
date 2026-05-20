<?php

declare(strict_types=1);

namespace App\Observer;

use App\Domain\Product\ProductInterface;
use App\Domain\Status\CookingStatus;

interface CookingObserverInterface
{
    public function onStatusChange(ProductInterface $product, CookingStatus $status): void;
}