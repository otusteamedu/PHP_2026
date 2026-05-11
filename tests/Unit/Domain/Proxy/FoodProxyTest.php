<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Proxy;

use App\Domain\Entity\Burger;
use App\Domain\Proxy\FoodProxy;
use PHPUnit\Framework\TestCase;

final class FoodProxyTest extends TestCase
{
    public function testItWorksLikeBurger(): void
    {
        $burger = new Burger('Cheeseburger');
        $proxy = new FoodProxy($burger);

        $result = $proxy->cook();

        self::assertCount(3, $result);
    }
}
