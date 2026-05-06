<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Services\PublishedPageCache;
use Illuminate\Console\Command;

class BenchmarkPageCache extends Command
{
    protected $signature = 'cache:benchmark-pages {--iterations=300}';

    protected $description = 'Compare average load time for published page lookup with and without cache';

    public function handle(PublishedPageCache $publishedPageCache): int
    {
        $iterations = max(1, (int) $this->option('iterations'));

        $page = Page::query()->where('is_published', true)->first();
        if ($page === null) {
            $page = Page::factory()->create([
                'slug' => 'bench-page-slug',
                'is_published' => true,
            ]);
        }

        $slug = $page->slug;

        $publishedPageCache->forget($slug);

        $t0 = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            Page::query()->where('slug', $slug)->where('is_published', true)->first();
        }
        $directSeconds = microtime(true) - $t0;

        $publishedPageCache->forget($slug);
        $publishedPageCache->findPublished($slug);

        $t0 = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $publishedPageCache->findPublished($slug);
        }
        $cachedSeconds = microtime(true) - $t0;

        $this->table(
            ['Mode', 'Total s', 'Per iteration ms'],
            [
                ['database', number_format($directSeconds, 4), number_format($directSeconds / $iterations * 1000, 4)],
                ['cache', number_format($cachedSeconds, 4), number_format($cachedSeconds / $iterations * 1000, 4)],
            ]
        );

        return self::SUCCESS;
    }
}
