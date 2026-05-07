<?php

namespace App\Providers;

use App\Models\Direction;
use App\Models\Page;
use App\Models\User;
use App\Observers\DirectionObserver;
use App\Observers\PageObserver;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Page::observe(PageObserver::class);
        Direction::observe(DirectionObserver::class);

        Event::listen(CacheMissed::class, function (CacheMissed $event): void {
            if (! app()->environment('local')) {
                return;
            }
            if (str_contains($event->key, 'pages.published.')) {
                Log::debug('cache_miss', ['key' => $event->key]);
            }
        });

        Paginator::useBootstrapFive();

        Gate::define('access-admin', function (User $user): bool {
            return $user->roles()->where('slug', 'admin')->exists();
        });
    }
}
