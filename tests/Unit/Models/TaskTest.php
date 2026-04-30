<?php

namespace Tests\Unit\Models;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskTest extends TestCase
{
    #[Test]
    public function fillable_contains_expected_keys(): void
    {
        $task = new Task;

        $this->assertSame(
            ['user_id', 'title', 'description', 'is_done', 'due_at'],
            $task->getFillable()
        );
    }

    #[Test]
    public function boolean_and_datetime_casts_apply_without_persistence(): void
    {
        $task = new Task;
        $task->setRawAttributes([
            'is_done' => 1,
            'due_at' => '2026-04-29 12:00:00',
        ]);

        $this->assertTrue($task->is_done);
        $this->assertSame('2026-04-29 12:00:00', $task->due_at->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function user_relation_is_belongsto_user(): void
    {
        $task = new Task;
        $relation = $task->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(User::class, $relation->getRelated());
    }
}
