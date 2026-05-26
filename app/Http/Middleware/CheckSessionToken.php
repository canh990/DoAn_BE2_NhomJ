<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhienDangNhap;
use Illuminate\Support\Str;
use App\Helpers\DeviceHelper;

class CheckSessionToken
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
        if (Auth::check()) {
            $token = session('session_token');

            if (!$token) {
                // Tự động tạo token phiên mới và ghi nhận phiên cho người dùng này
                $token = Str::random(64);
                session(['session_token' => $token]);

                $userAgent = $request->userAgent();
                $browser = DeviceHelper::getBrowser($userAgent);
                $platform = DeviceHelper::getPlatform($userAgent);
                $deviceName = DeviceHelper::getDeviceName($platform, $browser);

                // Đặt tất cả phiên khác thành la_phien_hien_tai = false
                PhienDangNhap::where('nguoi_dung_id', Auth::id())
                    ->update(['la_phien_hien_tai' => false]);

                PhienDangNhap::create([
                    'nguoi_dung_id' => Auth::id(),
                    'token_hash' => hash('sha256', $token),
                    'ten_thiet_bi' => $deviceName,
                    'trinh_duyet' => $browser,
                    'he_dieu_hanh' => $platform,
                    'user_agent' => $userAgent,
                    'dia_chi_ip' => $request->ip(),
                    'lan_hoat_dong_cuoi' => now(),
                    'la_phien_hien_tai' => true,
                    'het_han' => now()->addDays(30), // Mặc định hết hạn sau 30 ngày
                    'ngay_tao' => now(),
                ]);
            } else {
                $sessionRecord = PhienDangNhap::where('token_hash', hash('sha256', $token))
                    ->where('nguoi_dung_id', Auth::id())
                    ->first();

                if (!$sessionRecord || $sessionRecord->dang_xuat_luc !== null || ($sessionRecord->het_han && $sessionRecord->het_han->isPast())) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('error', 'Phiên đăng nhập của bạn đã hết hạn hoặc đã bị đăng xuất từ xa.');
                }

                // Cập nhật hoạt động cuối và đặt các phiên khác của người dùng này về la_phien_hien_tai = false
                PhienDangNhap::where('nguoi_dung_id', Auth::id())
                    ->where('token_hash', '!=', hash('sha256', $token))
                    ->update(['la_phien_hien_tai' => false]);

                $sessionRecord->update([
                    'lan_hoat_dong_cuoi' => now(),
                    'la_phien_hien_tai' => true,
                ]);
            }
        }

        return $next($request);
    }
}
