<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // <--- THÊM DÒNG NÀY

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix lỗi độ dài key cho MySQL cũ
        Schema::defaultStringLength(191); 

        // If the app is configured to use database sessions but the DB isn't ready,
        // fallback to file sessions so the app can boot without requiring session table.
        // This is a code-only safeguard (does not modify DB).
        try {
            if (config('session.driver') === 'database') {
                config(['session.driver' => 'file']);
            }
        } catch (\Exception $e) {
            config(['session.driver' => 'file']);
        }

        // Kiểm tra nếu đang chạy trên môi trường không phải local 
        // hoặc đơn giản là ép luôn khi đang test ngrok
        if (app()->environment('production') || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}