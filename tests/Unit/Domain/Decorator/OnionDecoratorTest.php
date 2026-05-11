<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Decorator;

use App\Domain\Decorator\OnionDecorator;
use App\Domain\Entity\Burger;
use App\Domain\Enum\Status;
use App\Domain\ValueObject\Ingredient;
use App\Domain\ValueObject\Onion;
use PHPUnit\Framework\TestCase;

final class OnionDecoratorTest extends TestCase
{
    public function testItAddsOnionDuringCooking(): void
    {
        $food = new Burger('Cheeseburger');
        $decoratedFood = new OnionDecorator($food);

        $result = $decoratedFood->cook();

        self::assertSame(Status::Cooked, $food->status);
        $filtered = array_filter($result, fn (Ingredient $ingredient) => $ingredient instanceof Onion);
        self::assertCount(1, $filtered);
    }
}
