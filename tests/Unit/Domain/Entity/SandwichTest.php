<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\Sandwich;
use PHPUnit\Framework\TestCase;

final class SandwichTest extends TestCase
{
    public function testItFillsSandwichName(): void
    {
        $sandwich = new Sandwich(
            name: 'Club Sandwich'
        );

        self::assertSame('Club Sandwich', $sandwich->getName());
    }
}
