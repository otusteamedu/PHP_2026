<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function root_redirects_to_default_locale(): void
    {
        $default = config('locale.default');

        $this->get('/')->assertRedirect('/'.$default);
    }

    #[Test]
    public function english_prefix_sets_app_locale(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/en/dashboard')->assertOk()->assertSee('en', false);
    }

    #[Test]
    public function russian_prefix_sets_app_locale(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/ru/dashboard')->assertOk()->assertSee('ru', false);
    }

    #[Test]
    public function invalid_locale_prefix_returns_not_found(): void
    {
        $this->get('/fr')->assertNotFound();
    }
}
