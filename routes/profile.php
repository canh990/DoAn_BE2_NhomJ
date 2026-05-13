<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::post('/profile/deactivate', [ProfileController::class, 'deactivate'])
        ->name('profile.deactivate');
    Route::post('/profile/send-action-otp', [ProfileController::class, 'sendActionOtp'])
        ->name('profile.send-action-otp');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    Route::post('/profile/remove-avatar', [ProfileController::class, 'removeAvatar'])
        ->name('profile.remove-avatar');
    Route::post('/profile/remove-cover', [ProfileController::class, 'removeCover'])
        ->name('profile.remove-cover');
    Route::post('/user/{user}/toggle-follow', [ProfileController::class, 'toggleFollow'])
        ->name('user.toggle-follow');
});

Route::get('/profile/{username}/followers', [ProfileController::class, 'followers'])
    ->name('profile.followers');

Route::get('/profile/{username}/following', [ProfileController::class, 'following'])
    ->name('profile.following');

Route::get('/profile/{username}', [ProfileController::class, 'showByUsername'])
    ->where('username', '^(?!edit$).+')
    ->name('profile.public');
Route::get('/profile/{username}', [ProfileController::class, 'showByUsername'])
    ->where('username', '^(?!edit$).+')
    ->middleware('profile.privacy')
    ->name('profile.public');
