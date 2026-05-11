<?php

declare(strict_types=1);

namespace App\Domain\Proxy;

use App\Domain\Entity\Cookable;

final readonly class FoodProxy implements Cookable
{
    public function __construct(
        private Cookable $food
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
