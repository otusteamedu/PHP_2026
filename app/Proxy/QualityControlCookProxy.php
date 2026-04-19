<?php

declare(strict_types=1);

namespace App\Proxy;

use App\Domain\Product\ProductInterface;
use App\Domain\Status\CookingStatus;
use App\Observer\CookingSubjectInterface;

final class QualityControlCookProxy implements CookInterface
{
    public function __construct(
        private readonly RealCook $realCook,
        private readonly CookingSubjectInterface $notifier,
    ) {
    }

    public function cook(ProductInterface $product): ?ProductInterface
    {
        $this->notifier->notify($product, CookingStatus::Accepted);

        if (!$this->preCheck($product)) {
            $this->notifier->notify($product, CookingStatus::Rejected);
            return null;
        }

        $cooked = $this->realCook->cook($product);

        $this->notifier->notify($cooked, CookingStatus::QualityCheck);

        if (!$this->postCheck($cooked)) {
            $this->notifier->notify($cooked, CookingStatus::Rejected);
            return null;
        }

        $this->notifier->notify($cooked, CookingStatus::Ready);
        return $cooked;
    }

    private function preCheck(ProductInterface $product): bool
    {
        return $product->getIngredients() !== [];
    }

    private function postCheck(ProductInterface $product): bool
    {
        return $product->getPrice() <= 1000.0;
    }
}