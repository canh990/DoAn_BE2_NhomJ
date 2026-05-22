<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\GroupChatController;
use Illuminate\Support\Facades\Route;

// Các route liên quan tới chat được nhóm dưới middleware auth để chỉ người đăng nhập mới truy cập được.
Route::middleware('auth')->group(function () {
    Route::get('/chat1-1', [ChatController::class, 'index'])->name('chat.demo');
    Route::post('/chat1-1/friends', [ChatController::class, 'storeFriend'])->name('chat.friends.store');
    Route::get('/chat1-1/users/{user}/messages', [ChatController::class, 'messagesForUser'])->name('chat.user.messages.index');
    Route::post('/chat1-1/users/{user}/messages', [ChatController::class, 'storeUserMessage'])->name('chat.user.messages.store');
    Route::post('/chat1-1/users/{user}/mute', [ChatController::class, 'toggleUserMute'])->name('chat.user.mute');
    Route::get('/chat1-1/users/{user}/typing', [ChatController::class, 'typingUsersForUser'])->name('chat.user.typing.index');
    Route::post('/chat1-1/users/{user}/typing', [ChatController::class, 'startTypingForUser'])->name('chat.user.typing.start');
    Route::delete('/chat1-1/users/{user}/typing', [ChatController::class, 'stopTypingForUser'])->name('chat.user.typing.stop');
    Route::post('/chat1-1/conversations', [ChatController::class, 'storeConversation'])->name('chat.conversations.store');
    Route::post('/chat1-1/conversations/{conversation}/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');

    Route::get('/chat-groups', [GroupChatController::class, 'index'])->name('chat.groups.index');
    Route::post('/chat-groups', [GroupChatController::class, 'store'])->name('chat.groups.store');
    Route::get('/chat-groups/{conversation}/messages', [GroupChatController::class, 'messages'])->name('chat.groups.messages.index');
    Route::post('/chat-groups/{conversation}/messages', [GroupChatController::class, 'storeMessage'])->name('chat.groups.messages.store');
    Route::post('/chat-groups/{conversation}/mute', [GroupChatController::class, 'toggleMute'])->name('chat.groups.mute');
    Route::get('/chat-groups/{conversation}/typing', [GroupChatController::class, 'typingUsers'])->name('chat.groups.typing.index');
    Route::post('/chat-groups/{conversation}/typing', [GroupChatController::class, 'startTyping'])->name('chat.groups.typing.start');
    Route::delete('/chat-groups/{conversation}/typing', [GroupChatController::class, 'stopTyping'])->name('chat.groups.typing.stop');
});
