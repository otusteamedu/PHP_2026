<?php

namespace Tests\Feature\Api;

use App\Models\Construct;
use App\Models\ConstructAlias;
use App\Models\ConstructLink;
use App\Models\ConstructSnippet;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructPublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_empty_for_unknown_language(): void
    {
        $response = $this->getJson('/api/v1/constructs?language=unknown&q=foreach');

        $response->assertOk()->assertExactJson(['data' => []]);
    }

    public function test_search_filters_by_language_and_query(): void
    {
        $php = Language::factory()->create(['code' => 'php', 'name' => 'PHP']);
        $go = Language::factory()->create(['code' => 'go', 'name' => 'Go']);

        Construct::factory()->for($go)->create(['title' => 'for loop', 'slug' => 'for']);
        Construct::factory()->for($php)->create(['title' => 'foreach', 'slug' => 'foreach', 'summary' => 'Loop over arrays']);
        Construct::factory()->for($php)->create(['title' => 'switch', 'slug' => 'switch']);

        $response = $this->getJson('/api/v1/constructs?language=php&q=foreach&limit=10');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('php', $data[0]['language']);
        $this->assertSame('foreach', $data[0]['slug']);
    }

    public function test_search_finds_by_alias(): void
    {
        $php = Language::factory()->create(['code' => 'php', 'name' => 'PHP']);
        $construct = Construct::factory()->for($php)->create(['title' => 'foreach', 'slug' => 'foreach']);
        ConstructAlias::factory()->for($construct)->create(['alias' => 'for each']);

        $response = $this->getJson('/api/v1/constructs?language=php&q=for%20each&limit=10');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('foreach', $data[0]['slug']);
    }

    public function test_show_returns_full_card_with_snippets_and_links(): void
    {
        $php = Language::factory()->create(['code' => 'php', 'name' => 'PHP']);
        $construct = Construct::factory()->for($php)->create([
            'title' => 'foreach',
            'slug' => 'foreach',
            'details' => 'Details text',
        ]);

        ConstructSnippet::factory()->for($construct)->create([
            'title' => 'Example',
            'code' => <<<'PHP'
<?php
foreach ([1, 2] as $x) {}
PHP,
            'sort' => 0,
        ]);
        ConstructLink::factory()->for($construct)->create([
            'title' => 'Docs',
            'url' => 'https://www.php.net/manual/en/control-structures.foreach.php',
            'sort' => 0,
        ]);

        $response = $this->getJson('/api/v1/constructs/php/foreach');

        $response->assertOk()
            ->assertJsonPath('language', 'php')
            ->assertJsonPath('slug', 'foreach')
            ->assertJsonPath('title', 'foreach')
            ->assertJsonPath('details', 'Details text')
            ->assertJsonPath('snippets.0.title', 'Example')
            ->assertJsonPath('links.0.title', 'Docs');
    }

    public function test_show_returns_404_for_unknown_slug(): void
    {
        Language::factory()->create(['code' => 'php', 'name' => 'PHP']);

        $response = $this->getJson('/api/v1/constructs/php/not-exists');

        $response->assertNotFound();
    }
}
