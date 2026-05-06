<?php

namespace Tests\Feature\Api;

use App\Models\Task;
use App\Models\User;

class TaskApiTest extends ApiTestCase
{
    public function test_authenticated_user_lists_only_own_tasks(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $mine = Task::factory()->for($owner)->create(['title' => 'Mine']);
        Task::factory()->for($other)->create(['title' => 'Not mine']);

        $token = $this->obtainAccessToken($owner->email, 'password');

        $response = $this->withToken($token)->getJson('/api/v1/tasks');

        $response->assertOk();
        $titles = collect($response->json())->pluck('title')->all();
        $this->assertContains('Mine', $titles);
        $this->assertNotContains('Not mine', $titles);
        $this->assertTrue(collect($response->json())->every(fn (array $row) => (int) $row['user_id'] === $owner->id));
    }
}
