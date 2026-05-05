<?php

use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

Route::view('/about', 'pages.static-info')->name('static.info');

Route::get('/page/{page}', function (Page $page) {
    if (! $page->is_published) {
        abort(404);
    }

    return view('pages.dynamic', ['page' => $page]);
})->name('page.show');

Route::get('login', [LoginController::class, 'create'])->name('login');
Route::post('login', [LoginController::class, 'store']);
Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store']);
Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('profile', [ProfileController::class, 'show'])->name('user.profile');
    Route::resource('tasks', TaskController::class)->except(['show']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.pages.index'))->name('dashboard');
    Route::resource('pages', PageController::class)->except(['show']);
    Route::resource('courses', CourseController::class)->except(['show']);
});
