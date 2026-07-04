<?php

use App\HTTP\Routing\Route;
use App\HTTP\Controllers\EmailController;

Route::post('/api/email-validation', [EmailController::class, 'check']);
Route::get('/api/email-validation/{request_id}', [EmailController::class, 'status']);