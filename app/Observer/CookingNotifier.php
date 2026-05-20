<?php

declare(strict_types=1);

namespace App\Observer;

use App\Domain\Product\ProductInterface;
use App\Domain\Status\CookingStatus;

final class CookingNotifier implements CookingSubjectInterface
{
    /** @var array<int, CookingObserverInterface> */
    private array $observers = [];

    public function attach(CookingObserverInterface $observer): void
    {
        $this->observers[spl_object_id($observer)] = $observer;
    }

    public function detach(CookingObserverInterface $observer): void
    {
        unset($this->observers[spl_object_id($observer)]);
    }

    public function notify(ProductInterface $product, CookingStatus $status): void
    {
        foreach ($this->observers as $observer) {
            $observer->onStatusChange($product, $status);
        }
    }
}