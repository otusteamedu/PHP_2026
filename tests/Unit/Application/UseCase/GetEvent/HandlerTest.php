<?php

declare(strict_types=1);

namespace Test\Unit\Application\UseCase\GetEvent;

use App\Application\UseCase\GetEvent\Handler;
use App\Application\UseCase\GetEvent\Request;
use App\Application\UseCase\GetEvent\Response;
use App\Domain\Entity\AnalyticEvent;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Conditions;
use App\Domain\ValueObject\Event;
use App\Domain\ValueObject\Priority;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Handler::class)]
#[CoversClass(Request::class)]
#[CoversClass(Response::class)]
final class HandlerTest extends TestCase
{
    public function testSuccess(): void
    {
        $repository = $this->createMock(EventRepositoryInterface::class);
        $handler = new Handler($repository);

        $request = new Request(['key' => 123]);

        $eventValue = 'event_1';
        $analyticEvent = new AnalyticEvent(
            Priority::create(1),
            Conditions::create(['key' => 123]),
            Event::create($eventValue)
        );

        $repository->expects($this->once())
            ->method('findPriorityOneByConditions')
            ->willReturn($analyticEvent);

        $result = $handler($request);

        $this->assertSame($eventValue, $result->event);
    }
}
