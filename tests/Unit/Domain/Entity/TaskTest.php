<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\Task;
use App\Domain\Enum\Status;
use App\Domain\ValueObject\Id;
use DomainException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = new Task($id = Id::generate(), $status = Status::InProgress);

        self::assertEquals($id, $task->id);
        self::assertEquals($status, $task->getStatus());
    }

    public function testNew(): void
    {
        $task = Task::new();

        self::assertNotEmpty($task->id);
        self::assertEquals(Status::Created, $task->getStatus());
    }

    public function testStartSuccess(): void
    {
        $task = Task::new();
        $task->start();

        self::assertEquals(Status::InProgress, $task->getStatus());
    }

    #[TestWith([Status::InProgress])]
    #[TestWith([Status::Completed])]
    #[TestWith([Status::Cancelled])]
    public function testStartWithException(Status $fromStatus): void
    {
        $task = new Task(Id::generate(), $fromStatus);

        self::expectException(DomainException::class);

        $task->start();
    }

    public function testFinishSuccess(): void
    {
        $task = new Task(Id::generate(), Status::InProgress);

        $task->finish();

        self::assertEquals(Status::Completed, $task->getStatus());
    }

    #[TestWith([Status::Created])]
    #[TestWith([Status::Completed])]
    #[TestWith([Status::Cancelled])]
    public function testFinishWithException(Status $fromStatus): void
    {
        $task = new Task(Id::generate(), $fromStatus);

        self::expectException(DomainException::class);

        $task->finish();
    }

    #[TestWith([Status::Created])]
    #[TestWith([Status::Completed])]
    #[TestWith([Status::Cancelled])]
    #[TestWith([Status::Completed])]
    public function testCancelSuccess(Status $fromStatus): void
    {
        $task = new Task(Id::generate(), $fromStatus);

        $task->cancel();

        self::assertEquals(Status::Cancelled, $task->getStatus());
    }
}
