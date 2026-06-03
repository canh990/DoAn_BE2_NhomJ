<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\User;

// Hằng ngày quét và xóa vĩnh viễn các tài khoản đã bị xóa mềm quá 30 ngày
Schedule::call(function () {
    User::onlyTrashed()
        ->where('ngay_xoa', '<=', now()->subDays(30))
        ->forceDelete();
})->daily();

