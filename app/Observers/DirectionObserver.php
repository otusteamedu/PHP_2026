<?php

namespace App\Observers;

use App\Models\Direction;
use App\Services\DirectionsListCache;

class DirectionObserver
{
    public function saved(Direction $direction): void
    {
        app(DirectionsListCache::class)->forget();
    }

    public function deleted(Direction $direction): void
    {
        app(DirectionsListCache::class)->forget();
    }
}
