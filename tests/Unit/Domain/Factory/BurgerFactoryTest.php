<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Factory;

use App\Domain\Entity\Burger;
use App\Domain\Enum\Status;
use App\Infrastructure\Factory\BurgerFactory;
use PHPUnit\Framework\TestCase;

final class BurgerFactoryTest extends TestCase
{
    public function testItCreatesBurger(): void
    {
        $factory = new BurgerFactory();

        $burger = $factory->create('Cheeseburger');

        self::assertInstanceOf(Burger::class, $burger);
        self::assertSame('Cheeseburger', $burger->getName());
        self::assertSame(Status::Created, $burger->status);
    }
}
