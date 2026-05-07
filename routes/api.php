<?php

use App\Http\Controllers\Api\Admin\ConstructAdminController;
use App\Http\Controllers\Api\Admin\ConstructLinkAdminController;
use App\Http\Controllers\Api\Admin\ConstructSnippetAdminController;
use App\Http\Controllers\Api\Admin\LanguageAdminController;
use App\Http\Controllers\Api\ConstructPublicApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/constructs', [ConstructPublicApiController::class, 'index']);
    Route::get('/constructs/{language}/{slug}', [ConstructPublicApiController::class, 'show'])
        ->where(['language' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);

    Route::prefix('admin')->middleware('admin.token')->group(function (): void {
        Route::apiResource('languages', LanguageAdminController::class);
        Route::apiResource('constructs', ConstructAdminController::class);

        Route::post('constructs/{construct}/snippets', [ConstructSnippetAdminController::class, 'store']);
        Route::patch('snippets/{snippet}', [ConstructSnippetAdminController::class, 'update']);
        Route::delete('snippets/{snippet}', [ConstructSnippetAdminController::class, 'destroy']);

        Route::post('constructs/{construct}/links', [ConstructLinkAdminController::class, 'store']);
        Route::patch('links/{link}', [ConstructLinkAdminController::class, 'update']);
        Route::delete('links/{link}', [ConstructLinkAdminController::class, 'destroy']);
    });
});
