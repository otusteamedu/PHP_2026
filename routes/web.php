<?php

use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

Route::view('/profile', 'pages.user')->name('user.profile');

Route::view('/register', 'pages.register')->name('register.form');

Route::view('/about', 'pages.static-info')->name('static.info');

Route::get('/page/{page}', function (Page $page) {
    if (! $page->is_published) {
        abort(404);
    }

    return view('pages.dynamic', ['page' => $page]);
})->name('page.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.attempt');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/', fn () => redirect()->route('admin.pages.index'))->name('dashboard');

        Route::resource('pages', PageController::class)->except(['show']);
        Route::resource('courses', CourseController::class)->except(['show']);
    });
});
