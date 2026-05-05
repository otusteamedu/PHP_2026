<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

Route::get('/profile', [ProfileController::class, 'show'])->name('user.profile');

Route::view('/register', 'pages.register')->name('register.form');
Route::post('/register', [RegisterController::class, 'store'])->name('register');

Route::view('/about', 'pages.static-info')->name('static.info');
