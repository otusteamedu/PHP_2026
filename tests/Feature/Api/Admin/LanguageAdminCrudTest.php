<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.admin.api_token' => 'secret']);
    }

    public function test_can_create_update_delete_language(): void
    {
        $create = $this->withHeader('Authorization', 'Bearer secret')->postJson('/api/v1/admin/languages', [
            'code' => 'php',
            'name' => 'PHP',
        ]);
        $create->assertCreated();
        $id = (int) $create->json('id');

        $update = $this->withHeader('Authorization', 'Bearer secret')->patchJson("/api/v1/admin/languages/{$id}", [
            'name' => 'PHP 8',
        ]);
        $update->assertOk()->assertJsonPath('name', 'PHP 8');

        $this->assertSame('PHP 8', Language::query()->findOrFail($id)->name);

        $delete = $this->withHeader('Authorization', 'Bearer secret')->deleteJson("/api/v1/admin/languages/{$id}");
        $delete->assertNoContent();
    }
}
