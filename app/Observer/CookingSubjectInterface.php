<?php

declare(strict_types=1);

namespace App\Observer;

use App\Domain\Product\ProductInterface;
use App\Domain\Status\CookingStatus;

interface CookingSubjectInterface
{
    public function attach(CookingObserverInterface $observer): void;

    public function detach(CookingObserverInterface $observer): void;

    public function notify(ProductInterface $product, CookingStatus $status): void;
}