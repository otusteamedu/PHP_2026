<?php

use App\Http\Controllers\Api\ApiTokenController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/token', [ApiTokenController::class, 'store']);

    Route::middleware('auth:api')->group(function (): void {
        Route::get('/tasks', [TaskApiController::class, 'list']);
        Route::get('/tasks/{task}', [TaskApiController::class, 'show']);
        Route::post('/tasks', [TaskApiController::class, 'store']);
        Route::put('/tasks/{task}', [TaskApiController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskApiController::class, 'destroy']);
    });
});
