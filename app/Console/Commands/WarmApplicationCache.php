<?php

namespace App\Console\Commands;

use App\Services\DirectionsListCache;
use App\Services\PublishedPageCache;
use Illuminate\Console\Command;

class WarmApplicationCache extends Command
{
    protected $signature = 'cache:warm-app
                            {target=all : Warm cache for: all, pages, or directions}
                            {--chunk=100 : Number of published pages to process per database batch}';

    protected $description = 'Warm application cache for published pages and/or the ordered directions list used in admin course forms';

    public function handle(
        PublishedPageCache $publishedPageCache,
        DirectionsListCache $directionsListCache
    ): int {
        $target = strtolower(trim((string) $this->argument('target')));
        $chunk = max(1, (int) $this->option('chunk'));

        if (! in_array($target, ['all', 'pages', 'directions'], true)) {
            $this->error('Invalid target. Use: all, pages, or directions.');

            return self::FAILURE;
        }

        if (in_array($target, ['all', 'pages'], true)) {
            $pages = $publishedPageCache->warmPublished($chunk);
            $this->info('Pages warmed: '.$pages);
        }

        if (in_array($target, ['all', 'directions'], true)) {
            $directionsListCache->allOrderedByName();
            $this->info('Directions list cached.');
        }

        return self::SUCCESS;
    }
}
