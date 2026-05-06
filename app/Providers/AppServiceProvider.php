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
        Schema::defaultStringLength(255); 

        // Kiểm tra nếu đang chạy trên môi trường không phải local 
        // hoặc đơn giản là ép luôn khi đang test ngrok
        if (app()->environment('production') || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}