<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\PersonalSettingsController;
use App\Http\Controllers\ChatController;

// Các route phụ trợ cho Settings (Đặt ngoài group để tự do custom name)
Route::post('/settings/cache-clear', [PersonalSettingsController::class, 'clearCache'])->name('cache.clear')->middleware('auth');
Route::delete('/settings/account-disable', [PersonalSettingsController::class, 'disableAccount'])->name('account.disable')->middleware('auth');

// --- ĐIỀU KHOẢN & CHÍNH SÁCH ĐÃ CHUYỂN THÀNH MODAL (XÓA 2 ROUTE CŨ TẠI ĐÂY) ---

// Lưu ý: Nếu hệ thống của bạn đã có route('logout') từ chức năng đăng nhập/đăng xuất chung thì có thể XÓA dòng này. 
Route::post('/logout', [PersonalSettingsController::class, 'logout'])->name('logout')->middleware('auth');


// All routes under /settings are defined here so your team's settings code stays separate
Route::prefix('settings')->name('settings.')->middleware('auth')->group(function () {
    // Main settings page
    Route::get('/', [PersonalSettingsController::class, 'index'])->name('index');

    // Personal sub-endpoints (your team's area)
    Route::get('/personal', [PersonalSettingsController::class, 'index'])->name('personal.index');
    Route::post('/personal/theme', [PersonalSettingsController::class, 'setTheme'])->name('personal.setTheme');
    Route::post('/personal/language', [PersonalSettingsController::class, 'setLanguage'])->name('personal.setLanguage');
    
    // Ví dụ khai báo route
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
});