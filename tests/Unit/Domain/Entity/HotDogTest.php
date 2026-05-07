<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\HotDog;
use PHPUnit\Framework\TestCase;

final class HotDogTest extends TestCase
{
    public function testItFillsHotDogName(): void
    {
        $hotDog = new HotDog(
            name: 'Classic Hot Dog'
        );

        self::assertSame('Classic Hot Dog', $hotDog->getName());
    }
}
