<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshApplicationCache extends Command
{
    protected $signature = 'cache:refresh-app
                            {target=all : Refresh cache for: all, pages, or directions}
                            {--chunk=100 : Number of published pages to process per database batch}';

    protected $description = 'Clear application cache and warm it again';

    public function handle(): int
    {
        $target = (string) $this->argument('target');
        $chunk = (int) $this->option('chunk');

        $this->call('cache:clear');

        Artisan::call('cache:warm-app', [
            'target' => $target,
            '--chunk' => $chunk,
        ], $this->output);

        return self::SUCCESS;
    }
}

