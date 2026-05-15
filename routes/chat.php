<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\GroupChatController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/chat1-1', [ChatController::class, 'index'])->name('chat.demo');
    Route::post('/chat1-1/friends', [ChatController::class, 'storeFriend'])->name('chat.friends.store');
    Route::get('/chat1-1/users/{user}/messages', [ChatController::class, 'messagesForUser'])->name('chat.user.messages.index');
    Route::post('/chat1-1/users/{user}/messages', [ChatController::class, 'storeUserMessage'])->name('chat.user.messages.store');
    Route::post('/chat1-1/conversations', [ChatController::class, 'storeConversation'])->name('chat.conversations.store');
    Route::post('/chat1-1/conversations/{conversation}/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');
    Route::delete('/chat1-1/messages/{message}', [ChatController::class, 'deleteMessage'])->name('chat.messages.destroy');
    Route::get('/chat1-1/conversations/{conversation}/search', [ChatController::class, 'searchMessages'])->name('chat.messages.search');

    Route::get('/chat-groups', [GroupChatController::class, 'index'])->name('chat.groups.index');
    Route::post('/chat-groups', [GroupChatController::class, 'store'])->name('chat.groups.store');
    Route::get('/chat-groups/{conversation}/messages', [GroupChatController::class, 'messages'])->name('chat.groups.messages.index');
    Route::post('/chat-groups/{conversation}/messages', [GroupChatController::class, 'storeMessage'])->name('chat.groups.messages.store');
    Route::delete('/chat-groups/messages/{message}', [GroupChatController::class, 'deleteMessage'])->name('chat.groups.messages.destroy');
    Route::get('/chat-groups/{conversation}/search', [GroupChatController::class, 'searchMessages'])->name('chat.groups.messages.search');
});

