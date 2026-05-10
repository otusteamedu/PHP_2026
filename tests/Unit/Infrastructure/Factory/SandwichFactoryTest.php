<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Factory;

use App\Domain\Entity\Sandwich;
use App\Domain\Enum\Status;
use App\Infrastructure\Factory\SandwichFactory;
use PHPUnit\Framework\TestCase;

final class SandwichFactoryTest extends TestCase
{
    public function testItCreatesSandwich(): void
    {
        $factory = new SandwichFactory();

        $sandwich = $factory->create('Club Sandwich');

        self::assertInstanceOf(Sandwich::class, $sandwich);
        self::assertSame('Club Sandwich', $sandwich->getName());
        self::assertSame(Status::Created, $sandwich->status);
    }
}
