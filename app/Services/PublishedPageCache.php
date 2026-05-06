<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PublishedPageCache
{
    public static function cacheKey(string $slug): string
    {
        return 'pages.published.'.hash('sha256', $slug);
    }

    public function findPublished(string $slug): ?Page
    {
        $key = self::cacheKey($slug);
        $cached = Cache::get($key);
        if ($cached instanceof Page) {
            return $cached;
        }

        $page = Page::query()->where('slug', $slug)->where('is_published', true)->first();
        if ($page !== null) {
            Cache::put($key, $page, now()->addSeconds(config('hw_cache.pages_ttl')));
        }

        return $page;
    }

    public function forget(string $slug): void
    {
        Cache::forget(self::cacheKey($slug));
    }

    public function warmPublished(int $chunkSize = 100): int
    {
        $count = 0;
        Page::query()->where('is_published', true)->select(['id', 'slug'])->chunkById(max(1, $chunkSize), function ($pages) use (&$count): void {
            foreach ($pages as $page) {
                $this->findPublished($page->slug);
                $count++;
            }
        });

        return $count;
    }
}
