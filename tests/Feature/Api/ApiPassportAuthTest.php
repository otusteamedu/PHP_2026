<?php

namespace Tests\Feature\Api;

use App\Models\User;

class ApiPassportAuthTest extends ApiTestCase
{
    public function test_token_issuing_returns_bearer_access_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'mobile-app',
        ]);

        $response->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonStructure(['access_token']);
    }

    public function test_token_rejects_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/token', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'mobile-app',
        ]);

        $response->assertUnprocessable();
    }

    public function test_tasks_list_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/tasks');

        $response->assertUnauthorized();
    }
}
