<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/chat1-1', [ChatController::class, 'index'])->name('chat.demo');
    Route::post('/chat1-1/friends', [ChatController::class, 'storeFriend'])->name('chat.friends.store');
    Route::get('/chat1-1/users/{user}/messages', [ChatController::class, 'messagesForUser'])->name('chat.user.messages.index');
    Route::post('/chat1-1/users/{user}/messages', [ChatController::class, 'storeUserMessage'])->name('chat.user.messages.store');
    Route::post('/chat1-1/conversations', [ChatController::class, 'storeConversation'])->name('chat.conversations.store');
    Route::post('/chat1-1/conversations/{conversation}/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');
});
