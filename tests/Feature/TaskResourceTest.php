<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_access_tasks(): void
    {
        $this->get(route('tasks.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function user_can_create_list_update_and_delete_own_task(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('tasks.index'))->assertOk();

        $store = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Изучить тесты',
            'description' => 'Покрытие Laravel',
            'is_done' => false,
        ]);

        $store->assertRedirect(route('tasks.index'));
        $store->assertSessionHas('ok');

        $task = Task::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($task);
        $this->assertSame('Изучить тесты', $task->title);

        $this->actingAs($user)->get(route('tasks.edit', $task))->assertOk();

        $update = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Изучить тесты — готово',
            'description' => null,
            'is_done' => true,
        ]);

        $update->assertRedirect(route('tasks.index'));
        $task->refresh();
        $this->assertTrue($task->is_done);

        $destroy = $this->actingAs($user)->delete(route('tasks.destroy', $task));
        $destroy->assertRedirect(route('tasks.index'));
        $this->assertNull(Task::query()->find($task->id));
    }

    #[Test]
    public function user_cannot_edit_another_users_task(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->actingAs($other)->get(route('tasks.edit', $task))->assertForbidden();
    }
}
