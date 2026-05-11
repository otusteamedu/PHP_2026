<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\HotDog;
use App\Domain\Enum\Status;
use PHPUnit\Framework\TestCase;

final class HotDogTest extends TestCase
{
    public function testItFillsHotDogName(): void
    {
        $hotDog = new HotDog(
            name: 'Classic Hot Dog'
        );

        self::assertSame('Classic Hot Dog', $hotDog->getName());
        self::assertSame(Status::Created, $hotDog->status);
    }

    public function testItCooksHotDog(): void
    {
        $hotDog = new HotDog(
            name: 'Classic Hot Dog'
        );

        self::assertSame(Status::Created, $hotDog->status);

        $result = $hotDog->cook();

        self::assertSame(Status::Cooked, $hotDog->status);
        self::assertCount(2, $result);
    }
}
