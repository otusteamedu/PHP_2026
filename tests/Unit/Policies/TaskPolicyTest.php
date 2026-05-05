<?php

namespace Tests\Unit\Policies;

use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    #[Test]
    public function view_any_and_create_allow_authenticated_user(): void
    {
        $policy = new TaskPolicy;
        $user = new User;
        $user->forceFill(['id' => 10]);

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->create($user));
    }

    #[Test]
    public function view_update_delete_require_ownership(): void
    {
        $policy = new TaskPolicy;
        $owner = new User;
        $owner->forceFill(['id' => 5]);
        $other = new User;
        $other->forceFill(['id' => 9]);

        $task = new Task;
        $task->forceFill(['user_id' => 5]);

        $this->assertTrue($policy->view($owner, $task));
        $this->assertTrue($policy->update($owner, $task));
        $this->assertTrue($policy->delete($owner, $task));

        $this->assertFalse($policy->view($other, $task));
        $this->assertFalse($policy->update($other, $task));
        $this->assertFalse($policy->delete($other, $task));
    }
}
