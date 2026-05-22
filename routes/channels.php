<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.conversation.{conversationId}', function ($user, int $conversationId) {
    return Conversation::query()
        ->whereKey($conversationId)
        ->whereHas('members', fn ($query) => $query->whereKey($user->id))
        ->exists();
});
