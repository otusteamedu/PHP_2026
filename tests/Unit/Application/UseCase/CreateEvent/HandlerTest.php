<?php

declare(strict_types=1);

namespace Test\Unit\Application\UseCase\CreateEvent;

use App\Application\UseCase\CreateEvent\Handler;
use App\Application\UseCase\CreateEvent\Request;
use App\Domain\Entity\AnalyticEvent;
use App\Domain\Repository\EventRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Handler::class)]
#[CoversClass(Request::class)]
final class HandlerTest extends TestCase
{
    public function testSuccess(): void
    {
        $repository = $this->createMock(EventRepositoryInterface::class);
        $handler = new Handler($repository);

        $request = new Request(
            priority: 1,
            conditions: ['key' => 123],
            event: 'test_event'
        );

        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (AnalyticEvent $event) use ($request) {
                return $event->priority->value === $request->priority
                    && count($event->conditions->params) === 1
                    && $event->conditions->params[0]->name === 'key'
                    && $event->conditions->params[0]->value === 123
                    && $event->event->value === $request->event;
            }));

        $handler($request);
    }
}
