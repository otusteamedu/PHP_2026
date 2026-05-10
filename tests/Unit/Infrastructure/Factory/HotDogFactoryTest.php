<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Factory;

use App\Domain\Entity\HotDog;
use App\Domain\Enum\Status;
use App\Infrastructure\Factory\HotDogFactory;
use PHPUnit\Framework\TestCase;

final class HotDogFactoryTest extends TestCase
{
    public function testItCreatesHotDog(): void
    {
        $factory = new HotDogFactory();

        $hotDog = $factory->create('Classic Hot Dog');

        self::assertInstanceOf(HotDog::class, $hotDog);
        self::assertSame('Classic Hot Dog', $hotDog->getName());
        self::assertSame(Status::Created, $hotDog->status);
    }
}
