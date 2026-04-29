<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

Route::view('/profile', 'pages.user')->name('user.profile');

Route::view('/register', 'pages.register')->name('register.form');

Route::view('/about', 'pages.static-info')->name('static.info');
