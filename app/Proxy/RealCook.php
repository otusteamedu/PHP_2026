<?php

declare(strict_types=1);

namespace App\Proxy;

use App\Domain\Product\ProductInterface;
use App\Domain\Status\CookingStatus;
use App\Observer\CookingSubjectInterface;

final class RealCook implements CookInterface
{
    public function __construct(private readonly CookingSubjectInterface $notifier)
    {
    }

    public function cook(ProductInterface $product): ProductInterface
    {
        $this->notifier->notify($product, CookingStatus::Preparing);
        $this->notifier->notify($product, CookingStatus::Cooking);

        return $product;
    }
}