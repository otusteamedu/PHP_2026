<?php

namespace App\Console\Commands;

use App\Services\DirectionsListCache;
use App\Services\PublishedPageCache;
use Illuminate\Console\Command;

class WarmApplicationCache extends Command
{
    protected $signature = 'cache:warm-app';

    protected $description = 'Preload published pages and admin directions list into cache';

    public function handle(
        PublishedPageCache $publishedPageCache,
        DirectionsListCache $directionsListCache
    ): int {
        $pages = $publishedPageCache->warmPublished();
        $this->output->writeln('Pages warmed: '.$pages);

        $directionsListCache->allOrderedByName();
        $this->output->writeln('Directions list cached.');

        return self::SUCCESS;
    }
}
