<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\Burger;
use PHPUnit\Framework\TestCase;

final class BurgerTest extends TestCase
{
    public function testItFillsBurgerName(): void
    {
        $burger = new Burger(
            name: 'Cheeseburger'
        );

        self::assertSame('Cheeseburger', $burger->getName());
    }
}
