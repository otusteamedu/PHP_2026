<?php

declare(strict_types=1);

namespace App\Application\Proxy;

use App\Application\Observer\StatusTracker;
use App\Domain\Entity\Cookable;
use App\Domain\Enum\Status;
use App\Domain\Proxy\CookInterface;
use App\Domain\Proxy\QualityCheckInterface;
use ArrayObject;

final readonly class CookProxy implements CookInterface
{
    /**
     * @param ArrayObject<int, QualityCheckInterface> $qualityCheckers
     */
    public function __construct(
        private CookInterface $cook,
        private StatusTracker $statusTracker,
        private ArrayObject $qualityCheckers,
    ) {
    }

    public function cook(Cookable $product): string
    {
        $this->beforeCook($product);

        $result = $this->cook->cook($product);

        return $this->afterCook($result);
    }

    private function beforeCook(Cookable $product): void
    {
        $this->statusTracker->init($product->getName());
        $this->statusTracker->updateStatus(Status::COOKING);
    }

    private function afterCook(string $resultProduct): string
    {
        if ($this->qualityCheck($resultProduct)) {
            $this->statusTracker->updateStatus(Status::DONE);
            return $resultProduct;
        }

        $this->statusTracker->updateStatus(Status::FAILED);
        return '';
    }

    private function qualityCheck(string $resultProduct): bool
    {
        foreach ($this->qualityCheckers as $qualityChecker) {
            if (!$qualityChecker->check($resultProduct)) {
                return false;
            }
        }
        return true;
    }
}
