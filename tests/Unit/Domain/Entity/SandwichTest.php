<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\Sandwich;
use App\Domain\Enum\Status;
use PHPUnit\Framework\TestCase;

final class SandwichTest extends TestCase
{
    public function testItFillsSandwichName(): void
    {
        $sandwich = new Sandwich(
            name: 'Club Sandwich'
        );

        self::assertSame('Club Sandwich', $sandwich->getName());
        self::assertSame(Status::Created, $sandwich->status);
    }

    public function testItCooksSandwich(): void
    {
        $sandwich = new Sandwich(
            name: 'Club Sandwich'
        );

        self::assertSame(Status::Created, $sandwich->status);

        $sandwich->cook();

        self::assertSame(Status::Cooked, $sandwich->status);
    }
}
