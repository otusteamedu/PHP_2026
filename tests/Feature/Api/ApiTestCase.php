<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:keys', ['--force' => true]);
        $this->artisan('passport:client', [
            '--personal' => true,
            '--name' => 'Testing Personal Access Client',
            '--provider' => 'users',
        ]);
    }

    protected function obtainAccessToken(string $email, string $password, string $deviceName = 'phpunit'): string
    {
        $response = $this->postJson('/api/v1/token', [
            'email' => $email,
            'password' => $password,
            'device_name' => $deviceName,
        ]);
        $response->assertOk();

        return (string) $response->json('access_token');
    }
}
