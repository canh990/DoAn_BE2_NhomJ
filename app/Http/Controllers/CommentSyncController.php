<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use Illuminate\Http\Request;

class CommentSyncController extends Controller
{
    public function thread(BaiViet $post)
    {
        $comments = $post->comments()
            ->with('user')
            ->get();

        return response()->json([
            'success' => true,
            'html' => view('components.comment-thread', [
                'comments' => $comments,
            ])->render(),
            'comments_count' => $post->comments()->count(),
        ]);
    }
}
