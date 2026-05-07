<?php

use App\Http\Controllers\Api\ConstructPublicApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/constructs', [ConstructPublicApiController::class, 'index']);
    Route::get('/constructs/{language}/{slug}', [ConstructPublicApiController::class, 'show'])
        ->where(['language' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
});
