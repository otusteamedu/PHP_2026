<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Decorator;

use App\Domain\Decorator\OnionDecorator;
use App\Domain\Entity\Burger;
use App\Domain\Enum\Status;
use PHPUnit\Framework\TestCase;

final class OnionDecoratorTest extends TestCase
{
    public function testItAddsOnionDuringCooking(): void
    {
        $food = new Burger('Cheeseburger');
        $decoratedFood = new OnionDecorator($food);

        $decoratedFood->cook();

        self::assertSame(Status::Cooked, $food->status);
    }
}
