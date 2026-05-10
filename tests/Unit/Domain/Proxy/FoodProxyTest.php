<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Proxy;

use App\Domain\Entity\Burger;
use App\Domain\Enum\Status;
use App\Domain\Proxy\FoodProxy;
use PHPUnit\Framework\TestCase;

final class FoodProxyTest extends TestCase
{
    public function testItWorksLikeBurger(): void
    {
        $burger = new Burger('Cheeseburger');
        $proxy = new FoodProxy($burger);

        $proxy->cook();

        self::assertSame(Status::Cooked, $proxy->status);
    }
}
