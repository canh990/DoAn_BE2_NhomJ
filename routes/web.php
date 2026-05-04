<?php
require __DIR__ . '/Auth.php';
require __DIR__ . '/profile.php';
use Illuminate\Support\Facades\Route;   

Route::get('/', function () {
    return view('welcome');
});
