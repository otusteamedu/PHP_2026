<?php

declare(strict_types=1);

namespace App\Domain\Proxy;

use App\Domain\Entity\Cookable;
use App\Domain\Enum\Status;

final class FoodProxy implements Cookable
{
    public Status $status {
        get {
            return $this->food->status;
        }
    }

    public function __construct(
        private readonly Cookable $food
    )
    {
    }

    public function cook(): array
    {
        $this->beforeCook();

        $result = $this->food->cook();

        $this->afterCook();

        return $result;
    }

    private function beforeCook(): void
    {
        // TODO: Implement method.
    }

    private function afterCook(): void
    {
        // TODO: Implement method.
    }
}
