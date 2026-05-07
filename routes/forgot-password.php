<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;

// 1. Trang hiển thị form nhập Email
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

// 2. Xử lý gửi OTP (Đây chính là route đang bị báo thiếu)
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])
    ->name('password.email');

// 3. Trang hiển thị form nhập OTP
Route::get('/verify-otp', [ForgotPasswordController::class, 'showOtpForm'])
    ->name('password.otp.show');

// 4. Xử lý kiểm tra mã OTP
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])
    ->name('password.otp.verify');

// 5. Trang hiển thị form đặt mật khẩu mới
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset');

// 6. Xử lý cập nhật mật khẩu mới vào Database
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
    ->name('password.update');

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');
