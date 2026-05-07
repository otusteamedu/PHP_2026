<?php

namespace App\Services;

use App\Models\Direction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class DirectionsListCache
{
    public const CACHE_KEY = 'admin.directions.ordered';

    public function allOrderedByName(): Collection
    {
        return Cache::remember(self::CACHE_KEY, now()->addSeconds(config('hw_cache.directions_ttl')), function () {
            return Direction::query()->orderBy('name')->get();
        });
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
