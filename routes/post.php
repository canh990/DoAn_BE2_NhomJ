<?php


use App\Http\Controllers\HomeController;

use App\Http\Controllers\CommentController;

use App\Http\Controllers\PostController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------
// Post routes
// -----------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])
        ->name('home');

    Route::get('/posts/{post}', [PostController::class, 'show'])
        ->name('posts.show');

    Route::post('/posts', [PostController::class, 'store'])
        ->name('posts.store');

    Route::get('/posts/{post}', [PostController::class, 'show'])
        ->name('posts.show');

    Route::post('/posts/{post}/reaction', [\App\Http\Controllers\ReactionController::class, 'store'])
        ->name('posts.react');

    Route::post('/polls/{poll}/vote', [PostController::class, 'vote'])
        ->name('polls.vote');

    Route::post('/posts/{post}/share', [PostController::class, 'share'])
        ->name('posts.share');

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
        ->name('posts.comment');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    Route::put('/posts/{post}', [PostController::class, 'update'])
        ->name('posts.update');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy');

    // -----------------------------------------------
    // Story (Tin 24h) routes
    // -----------------------------------------------
    Route::get('/stories/create', [StoryController::class, 'create'])
        ->name('stories.create');

    Route::post('/stories', [StoryController::class, 'store'])
        ->name('stories.store');

    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])
        ->name('stories.destroy');

    // -----------------------------------------------
    // Bookmark routes
    // -----------------------------------------------
    Route::post('/posts/{post}/bookmark', [\App\Http\Controllers\BookmarkController::class, 'toggle'])
        ->name('posts.bookmark');

    Route::get('/bookmarks', [\App\Http\Controllers\BookmarkController::class, 'index'])
        ->name('bookmarks.index');

    // -----------------------------------------------
    // Mention suggestions API
    // -----------------------------------------------
    Route::get('/api/users/mention-suggestions', [\App\Http\Controllers\MentionController::class, 'suggestions'])
        ->name('api.users.mention-suggestions');
});