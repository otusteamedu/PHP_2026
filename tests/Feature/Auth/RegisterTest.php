<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    #[Test]
    public function guest_can_view_register_form(): void
    {
        $this->get(route('register'))->assertOk()->assertViewIs('auth.register');
    }

    #[Test]
    public function authenticated_user_redirected_from_register_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('register'))->assertRedirect(route('home'));
    }

    #[Test]
    public function registration_creates_user_and_assigns_student_role_when_present(): void
    {
        Role::factory()->create(['slug' => 'student', 'name' => 'Студент']);

        $response = $this->post(route('register'), [
            'name' => 'Новый пользователь',
            'email' => 'new@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'new@example.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->roles()->where('slug', 'student')->exists());
    }
}
