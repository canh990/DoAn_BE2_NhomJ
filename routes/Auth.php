<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\Auth\RegisterController;
<<<<<<< HEAD
use App\Http\Controllers\SearchController;

=======
use Illuminate\Support\Facades\App;
>>>>>>> bc9c934 (update: change language)
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

// -----------------------------------------------
// OTP Verification (xác thực email sau đăng ký)
// Bỏ qua bước này nếu login bằng Facebook/Google (mặc định tích xanh)
// -----------------------------------------------
Route::get('/verify-email-otp', [RegisterController::class, 'showOtpForm'])->name('otp.show')->middleware('auth');
Route::post('/verify-email-otp', [RegisterController::class, 'verifyOtp'])->name('otp.verify')->middleware('auth');
Route::post('/skip-email-verification', [RegisterController::class, 'skipVerification'])->name('otp.skip')->middleware('auth');
Route::post('/resend-email-otp', [RegisterController::class, 'resendOtp'])->name('otp.resend')->middleware('auth');

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
<<<<<<< HEAD
Route::get('/explore', [\App\Http\Controllers\PostController::class, 'explore'])
    ->name('explore')
    ->middleware('auth');
=======
Route::get('/explore', function () {
>>>>>>> bc9c934 (update: change language)

    App::setLocale(
        session('personal_locale', config('app.locale'))
    );

<<<<<<< HEAD
Route::view('/messages', 'components.placeholder', [
    'title' => __('messages.chat_title'),
    'message' => __('messages.chat_subtitle'),
])->name('messages')->middleware('auth');

Route::get('/search/users', [SearchController::class, 'searchUsers'])
    ->middleware('auth')
    ->name('search.users');
=======
    return view('components.placeholder', [
        'title' => __('messages.explore_title'),
        'message' => __('messages.explore_subtitle'),
    ]);

})->name('explore')->middleware('auth');

Route::get('/notifications', function () {

    App::setLocale(
        session('personal_locale', config('app.locale'))
    );

    return view('components.placeholder', [
        'title' => __('messages.notifications_title'),
        'message' => __('messages.notifications_subtitle'),
    ]);

})->name('notifications')->middleware('auth');

Route::get('/messages', function () {

    App::setLocale(
        session('personal_locale', config('app.locale'))
    );

    return view('components.placeholder', [
        'title' => __('messages.messages_title'),
        'message' => __('messages.messages_subtitle'),
    ]);

})->name('messages')->middleware('auth');
>>>>>>> bc9c934 (update: change language)
