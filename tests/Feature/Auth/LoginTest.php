<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    #[Test]
    public function guest_can_view_login_form(): void
    {
        $this->get(route('login'))->assertOk()->assertViewIs('auth.login');
    }

    #[Test]
    public function authenticated_user_redirected_from_login_create(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('login'))->assertRedirect(route('home'));
    }

    #[Test]
    public function login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'ok@example.test',
            'password' => 'password',
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'ok@example.test',
            'password' => 'wrong',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function login_fails_for_unknown_email_with_same_error_message_as_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'exists@example.test',
            'password' => 'password',
        ]);

        $wrongPassword = $this->from(route('login'))->post(route('login'), [
            'email' => 'exists@example.test',
            'password' => 'wrong-password',
        ]);

        $wrongPassword->assertSessionHasErrors('email');
        $expectedMessage = 'Неверный email или пароль.';
        $wrongEmailErrors = session('errors')->get('email');
        $this->assertContains($expectedMessage, $wrongEmailErrors);

        $unknownEmail = $this->from(route('login'))->post(route('login'), [
            'email' => 'nobody@example.test',
            'password' => 'password',
        ]);

        $unknownEmail->assertSessionHasErrors('email');
        $unknownEmailErrors = session('errors')->get('email');
        $this->assertContains($expectedMessage, $unknownEmailErrors);
        $this->assertEquals($wrongEmailErrors, $unknownEmailErrors);
        $this->assertGuest();
    }

    #[Test]
    public function student_redirects_to_home_after_login(): void
    {
        User::factory()->create([
            'email' => 'stu@example.test',
            'password' => 'password',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'stu@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function admin_redirects_to_admin_pages_after_login(): void
    {
        $adminRole = Role::factory()->create(['slug' => 'admin']);
        $admin = User::factory()->create([
            'email' => 'adm@example.test',
            'password' => 'password',
        ]);
        $admin->roles()->attach($adminRole);

        $response = $this->post(route('login'), [
            'email' => 'adm@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.pages.index'));
        $this->assertAuthenticatedAs($admin);
    }

    #[Test]
    public function logout_clears_session(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
