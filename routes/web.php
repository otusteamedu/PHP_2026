<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ConstructsController;
use App\Http\Controllers\KbController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/kb/{language}/{slug}', [KbController::class, 'show'])
    ->where(['language' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);

Route::get('/admin/login', [AuthController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.web')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/constructs', [ConstructsController::class, 'index'])->name('constructs.index');
    Route::get('/constructs/create', [ConstructsController::class, 'create'])->name('constructs.create');
    Route::post('/constructs', [ConstructsController::class, 'store'])->name('constructs.store');
    Route::get('/constructs/{construct}/edit', [ConstructsController::class, 'edit'])->name('constructs.edit');
    Route::put('/constructs/{construct}', [ConstructsController::class, 'update'])->name('constructs.update');
    Route::delete('/constructs/{construct}', [ConstructsController::class, 'destroy'])->name('constructs.destroy');

    Route::post('/constructs/{construct}/snippets', [ConstructsController::class, 'storeSnippet'])->name('constructs.snippets.store');
    Route::delete('/snippets/{snippet}', [ConstructsController::class, 'destroySnippet'])->name('snippets.destroy');

    Route::post('/constructs/{construct}/links', [ConstructsController::class, 'storeLink'])->name('constructs.links.store');
    Route::delete('/links/{link}', [ConstructsController::class, 'destroyLink'])->name('links.destroy');
});
