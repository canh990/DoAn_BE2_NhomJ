<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            if (!session()->has('personal_locale') || !session()->has('personal_theme')) {
                try {
                    $setting = \DB::table('cai_dat_nguoi_dung')
                        ->where('nguoi_dung_id', auth()->id())
                        ->first();
                    if ($setting) {
                        if (!session()->has('personal_locale')) {
                            session(['personal_locale' => 'vi']);
                        }
                        if (!session()->has('personal_theme')) {
                            session(['personal_theme' => $setting->che_do_toi ? 'dark' : 'light']);
                        }
                    }
                } catch (\Exception $e) {
                    // Tránh lỗi khi chạy CLI/Migrations
                }
            }
        }

        if (!session()->has('personal_locale')) {
            session(['personal_locale' => 'vi']);
        }

        App::setLocale(session('personal_locale'));

        return $next($request);
    }
}
