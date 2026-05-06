<?php
require __DIR__ . '/Auth.php';
require __DIR__ . '/forgot-password.php';
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
