<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_is_redirected_to_login(): void
    {
        $this->get(route('user.profile'))->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_sees_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('user.profile'))
            ->assertOk()
            ->assertViewIs('profile.show')
            ->assertViewHas('user');
    }
}
