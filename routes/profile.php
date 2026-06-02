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
    Route::post('/profile/send-verify-otp', [ProfileController::class, 'sendVerifyEmailOtp'])
        ->name('profile.send-verify-otp');
    Route::post('/profile/verify-email-otp', [ProfileController::class, 'verifyEmailOtp'])
        ->name('profile.verify-email-otp');
    Route::post('/profile/send-change-email-otp', [ProfileController::class, 'sendChangeEmailOtp'])
        ->name('profile.send-change-email-otp');
    Route::post('/profile/change-email', [ProfileController::class, 'changeEmail'])
        ->name('profile.change-email');
    Route::post('/user/{user}/toggle-follow', [ProfileController::class, 'toggleFollow'])
        ->name('user.toggle-follow');
    Route::post('/user/{follower}/accept-follow', [ProfileController::class, 'acceptFollow'])
        ->name('user.accept-follow');
    Route::post('/user/{follower}/decline-follow', [ProfileController::class, 'declineFollow'])
        ->name('user.decline-follow');
    Route::post('/user/{user}/block', [ProfileController::class, 'blockUser'])
        ->name('user.block');
    Route::post('/user/{user}/unblock', [ProfileController::class, 'unblockUser'])
        ->name('user.unblock');

    // Các Route AJAX tải thêm Nhật ký hoạt động
    Route::get('/profile/activity/liked', [ProfileController::class, 'activityLiked'])
        ->name('profile.activity.liked');
    Route::get('/profile/activity/comments', [ProfileController::class, 'activityComments'])
        ->name('profile.activity.comments');
    Route::get('/profile/activity/saved', [ProfileController::class, 'activitySaved'])
        ->name('profile.activity.saved');
});

Route::get('/profile/{username}/followers', [ProfileController::class, 'followers'])
    ->name('profile.followers');

Route::get('/profile/{username}/following', [ProfileController::class, 'following'])
    ->name('profile.following');

Route::get('/profile/{username}', [ProfileController::class, 'showByUsername'])
    ->where('username', '^(?!edit$).+')
    ->name('profile.public');
