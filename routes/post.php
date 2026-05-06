<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------
// Post routes
// -----------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])
        ->name('home');

    Route::post('/posts', [PostController::class, 'store'])
        ->name('posts.store');
});