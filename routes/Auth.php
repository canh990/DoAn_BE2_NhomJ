<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\Auth\RegisterController;
// -----------------------------------------------
// Auth routes
// -----------------------------------------------

// Hiển thị form đăng nhập
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

// Xử lý đăng nhập
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.post')
    ->middleware('guest');

// Đăng xuất
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post')->middleware('guest');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
// -----------------------------------------------
// OAuth (Google / Facebook) — cần cài laravel/socialite
// composer require laravel/socialite
// -----------------------------------------------
 Route::get('/auth/google',          [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
 Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);
 Route::get('/auth/facebook',          [SocialLoginController::class, 'redirectToFacebook'])->name('auth.facebook');
 Route::get('/auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);

// -----------------------------------------------
// Trang sau khi đăng nhập
// -----------------------------------------------
Route::get('/home', function () {
    return view('components.home');
})->name('home')->middleware('auth');