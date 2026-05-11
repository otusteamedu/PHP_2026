<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\Burger;
use App\Domain\Enum\Status;
use PHPUnit\Framework\TestCase;

final class BurgerTest extends TestCase
{
    public function testItFillsBurgerName(): void
    {
        $burger = new Burger(
            name: 'Cheeseburger'
        );

        self::assertSame('Cheeseburger', $burger->getName());
        self::assertSame(Status::Created, $burger->status);
    }

    public function testItCooksBurger(): void
    {
        $burger = new Burger(
            name: 'Cheeseburger'
        );

        self::assertSame(Status::Created, $burger->status);

        $result = $burger->cook();

        self::assertSame(Status::Cooked, $burger->status);
        self::assertCount(3, $result);
    }
}
