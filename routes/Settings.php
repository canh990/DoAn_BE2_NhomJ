<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\PersonalSettingsController;

// All routes under /settings are defined here so your team's settings code stays separate
Route::prefix('settings')->name('settings.')->middleware('auth')->group(function () {
    // Main settings page
    Route::get('/', [PersonalSettingsController::class, 'index'])->name('index');

    // Personal sub-endpoints (your team's area)
    Route::get('/personal', [PersonalSettingsController::class, 'index'])->name('personal.index');
    Route::post('/personal/theme', [PersonalSettingsController::class, 'setTheme'])->name('personal.setTheme');
});
