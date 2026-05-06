<?php

namespace Tests\Feature\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;

class ProfileCabinetApiTest extends ApiTestCase
{
    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertUnauthorized();
    }

    public function test_me_rejects_invalid_bearer_token(): void
    {
        $response = $this->withToken('not-a-valid-token', 'Bearer')->getJson('/api/v1/me');

        $response->assertUnauthorized();
    }

    public function test_me_returns_roles_and_profile_with_valid_token(): void
    {
        $role = Role::factory()->create(['name' => 'Студент', 'slug' => 'student']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        UserProfile::factory()->for($user)->create([
            'bio' => 'Bio text',
            'headline' => 'My headline',
        ]);

        $token = $this->obtainAccessToken($user->email, 'password');

        $response = $this->withToken($token, 'Bearer')->getJson('/api/v1/me');

        $response->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', $user->name)
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('roles.0.slug', 'student')
            ->assertJsonPath('profile.bio', 'Bio text')
            ->assertJsonPath('profile.headline', 'My headline');
    }

    public function test_me_returns_null_profile_when_missing(): void
    {
        $user = User::factory()->create();
        $token = $this->obtainAccessToken($user->email, 'password');

        $response = $this->withToken($token, 'Bearer')->getJson('/api/v1/me');

        $response->assertOk();
        $this->assertNull($response->json('profile'));
    }
}
