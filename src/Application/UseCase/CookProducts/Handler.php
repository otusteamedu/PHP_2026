<?php

declare(strict_types=1);

namespace App\Application\UseCase\CookProducts;

use App\Domain\Proxy\CookInterface;
use App\Domain\Strategy\CookingStrategyInterface;
use LogicException;

final readonly class Handler
{
    /** @param array<string, CookingStrategyInterface> $strategies */
    public function __construct(
        private CookInterface $cook,
        private array $strategies
    ) {

    }

    public function __invoke(Order $order): void
    {
        foreach ($order->positions as $position) {
            $product = $this->resolveStrategy($position->product)->getFactory()->create();
            $product = $position->configurator->configure($product);
            $this->cook->cook($product);
        }
    }

    private function resolveStrategy(string $product): CookingStrategyInterface
    {
        if (!isset($this->strategies[$product])) {
            throw new LogicException("Strategy for product $product not found");
        }

        return $this->strategies[$product];
    }
}
