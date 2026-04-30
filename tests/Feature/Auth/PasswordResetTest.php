<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_view_forgot_password_form(): void
    {
        $this->get(route('password.request'))->assertOk()->assertViewIs('auth.forgot-password');
    }

    #[Test]
    public function forgot_password_accepts_email_and_sets_flash_status(): void
    {
        User::factory()->create(['email' => 'reset@example.test']);

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => 'reset@example.test',
        ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('status');
    }

    #[Test]
    public function guest_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'token@example.test']);
        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token]).'?email='.urlencode($user->email))
            ->assertOk()
            ->assertViewIs('auth.reset-password');

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'New-valid-pass-1',
            'password_confirmation' => 'New-valid-pass-1',
        ]);

        $response->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('New-valid-pass-1', $user->password));
    }
}
