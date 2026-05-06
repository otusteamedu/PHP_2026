<?php

namespace App\Observers;

use App\Models\Page;
use App\Services\PublishedPageCache;

class PageObserver
{
    public function updating(Page $page): void
    {
        if ($page->isDirty('slug') && $page->getOriginal('slug') !== null) {
            app(PublishedPageCache::class)->forget((string) $page->getOriginal('slug'));
        }
    }

    public function saved(Page $page): void
    {
        app(PublishedPageCache::class)->forget($page->slug);
    }

    public function deleted(Page $page): void
    {
        app(PublishedPageCache::class)->forget($page->slug);
    }
}
