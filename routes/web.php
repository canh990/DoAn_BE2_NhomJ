<?php
require __DIR__ . '/Auth.php';

require __DIR__ . '/post.php';

require __DIR__ . '/chat.php';

use Illuminate\Support\Facades\Route;
require __DIR__ . '/profile.php';
  

Route::get('/', function () {
    return redirect()->route('login');
});

// Load separated settings routes (your personal team file)
require __DIR__ . '/Settings.php';
