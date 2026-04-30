<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicDynamicPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function published_page_is_visible(): void
    {
        $page = Page::factory()->create([
            'slug' => 'public-doc',
            'is_published' => true,
        ]);

        $this->get(route('page.show', $page))->assertOk()->assertViewIs('pages.dynamic');
    }

    #[Test]
    public function unpublished_page_returns_not_found(): void
    {
        $page = Page::factory()->create([
            'slug' => 'secret-doc',
            'is_published' => false,
        ]);

        $this->get(route('page.show', $page))->assertNotFound();
    }
}
