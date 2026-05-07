<?php

use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\SetLocaleFromUrl;
use App\Services\PublishedPageCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$localePattern = implode('|', config('locale.supported', ['en', 'ru']));

Route::redirect('/', '/'.config('locale.default', 'ru'), 302);

Route::prefix('{locale}')
    ->where(['locale' => $localePattern])
    ->middleware([SetLocaleFromUrl::class])
    ->group(function (): void {
        Route::view('/', 'pages.home')->name('home');

        Route::view('about', 'pages.static-info')->name('static.info');

        Route::get('page/{slug}', function (PublishedPageCache $publishedPageCache, Request $request) {
            $slug = (string) $request->route()->parameter('slug');

            $model = $publishedPageCache->findPublished($slug);
            if ($model === null) {
                abort(404);
            }

            return view('pages.dynamic', ['page' => $model]);
        })->where('slug', '[A-Za-z0-9_-]+')->name('page.show');

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
            Route::view('dashboard', 'dashboard')->name('dashboard');
            Route::resource('tasks', TaskController::class)->except(['show']);
        });

        Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:access-admin'])->group(function (): void {
            Route::get('/', fn () => redirect()->route('admin.pages.index'))->name('dashboard');
            Route::resource('pages', PageController::class)->except(['show']);
            Route::resource('courses', CourseController::class)->except(['show']);
        });
    });
