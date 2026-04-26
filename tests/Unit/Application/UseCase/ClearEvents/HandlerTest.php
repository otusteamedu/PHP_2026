<?php

declare(strict_types=1);

namespace Test\Unit\Application\UseCase\ClearEvents;

use App\Application\UseCase\ClearEvents\Handler;
use App\Domain\Repository\EventRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Handler::class)]
final class HandlerTest extends TestCase
{
    public function testSuccess(): void
    {
        $repository = $this->createMock(EventRepositoryInterface::class);
        $handler = new Handler($repository);

        $repository->expects($this->once())
            ->method('deleteAll');

        $handler();
    }
}
