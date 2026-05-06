<?php
require __DIR__ . '/Auth.php';
require __DIR__ . '/chat.php';

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
