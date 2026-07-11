<?php

use App\HTTP\Routing\Route;
use App\HTTP\Controllers\EmailController;
use App\HTTP\Controllers\HealthCheckController;

Route::post('/api/email-validation', [EmailController::class, 'check']);
Route::get('/api/email-validation/{request_id}', [EmailController::class, 'status']);
Route::get('/api/health', [HealthCheckController::class, 'check']);