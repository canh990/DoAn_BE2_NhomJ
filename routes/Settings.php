<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\PersonalSettingsController;

// --- THÊM CÁC ROUTE CÒN THIẾU VÀO ĐÂY ---
// Đặt ngoài group 'settings.' để tên route khớp chuẩn 100% với file Blade
Route::post('/settings/cache-clear', [PersonalSettingsController::class, 'clearCache'])->name('cache.clear')->middleware('auth');
Route::delete('/settings/account-disable', [PersonalSettingsController::class, 'disableAccount'])->name('account.disable')->middleware('auth');

// Lưu ý: Nếu hệ thống của bạn đã có route('logout') từ chức năng đăng nhập/đăng xuất chung thì có thể XÓA dòng này. 
// Nếu chưa có, hãy để lại để test giao diện.
Route::post('/logout', [PersonalSettingsController::class, 'logout'])->name('logout')->middleware('auth');


// All routes under /settings are defined here so your team's settings code stays separate
Route::prefix('settings')->name('settings.')->middleware('auth')->group(function () {
    // Main settings page
    Route::get('/', [PersonalSettingsController::class, 'index'])->name('index');

    // Personal sub-endpoints (your team's area)
    Route::get('/personal', [PersonalSettingsController::class, 'index'])->name('personal.index');
    Route::post('/personal/theme', [PersonalSettingsController::class, 'setTheme'])->name('personal.setTheme');
    
    // Persist language selection
    Route::post('/personal/language', [PersonalSettingsController::class, 'setLanguage'])->name('personal.setLanguage');
});