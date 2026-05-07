<?php

namespace Tests\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_endpoints_require_bearer_token(): void
    {
        $response = $this->getJson('/api/v1/admin/languages');

        $response->assertUnauthorized();
    }

    public function test_admin_endpoints_reject_wrong_token(): void
    {
        config(['services.admin.api_token' => 'secret']);

        $response = $this->withHeader('Authorization', 'Bearer wrong')->getJson('/api/v1/admin/languages');

        $response->assertUnauthorized();
    }
}
