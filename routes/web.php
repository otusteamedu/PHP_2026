<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\PageController;
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
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', fn () => redirect()->route('admin.pages.index'))->name('dashboard');

        Route::resource('pages', PageController::class)->except(['show']);
        Route::resource('courses', CourseController::class)->except(['show']);
    });
});
