<?php

declare(strict_types=1);

namespace App\Application\Proxy;

use App\Application\Observer\StatusTracker;
use App\Domain\Entity\Cookable;
use App\Domain\Enum\Status;
use App\Domain\Proxy\CookInterface;

final readonly class CookProxy implements CookInterface
{
    public function __construct(
        private CookInterface $cook,
        private StatusTracker $statusTracker
    ) {
    }

    public function cook(Cookable $product): void
    {
        $this->beforeCook();

        $this->cook->cook($product);

        $this->afterCook();
    }

    private function beforeCook(): void
    {
        $this->statusTracker->updateStatus(Status::COOKING);
    }

    private function afterCook(): void
    {
        $finalStatus = $this->qualityCheck() ? Status::DONE : Status::FAILED;
        $this->statusTracker->updateStatus($finalStatus);
    }

    private function qualityCheck(): bool
    {
        //todo implement
        return true;
    }
}
