<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helpers\DeviceHelper;
use App\Models\PhienDangNhap;
use Illuminate\Support\Str;

use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'Vui lòng nhập email hoặc số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $login    = trim($request->input('login'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // ✅ Chỉ cho phép đăng nhập bằng email hoặc số điện thoại
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (preg_match('/^[0-9+\s().-]+$/', $login)) {
            $field = 'so_dien_thoai';
        } else {
            throw ValidationException::withMessages([
                'auth_error' => 'Vui lòng nhập đúng định dạng email hoặc số điện thoại.',
            ]);
        }

        // Tìm người dùng bao gồm cả tài khoản đã xóa mềm
        $user = User::withTrashed()->where($field, $login)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->mat_khau_hash)) {
            throw ValidationException::withMessages([
                'auth_error' => 'Thông tin đăng nhập hoặc mật khẩu không đúng.',
            ]);
        }

        // Khôi phục tài khoản nếu bị xóa mềm (Soft Deleted) hoặc vô hiệu hóa
        if ($user->trashed()) {
            $user->restore();
            $user->update(['con_hoat_dong' => true]);
            session()->flash('success', 'Tài khoản của bạn đã được khôi phục thành công!');
        } elseif (!$user->con_hoat_dong) {
            $user->update(['con_hoat_dong' => true]);
            session()->flash('success', 'Tài khoản của bạn đã được khôi phục thành công!');
        }

        Auth::login($user, $remember);

        $request->session()->regenerate();

        $user = Auth::user();

        // user agent
        $userAgent = $request->userAgent();

        // detect browser
        $browser = 'Unknown Browser';

        if (str_contains($userAgent, 'Edg')) {
            $browser = 'Microsoft Edge';
        } elseif (str_contains($userAgent, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            $browser = 'Safari';
        }

        // detect hệ điều hành
        $platform = 'Unknown OS';

        if (str_contains($userAgent, 'Windows')) {
            $platform = 'Windows';
        } elseif (str_contains($userAgent, 'Macintosh')) {
            $platform = 'MacOS';
        } elseif (str_contains($userAgent, 'Android')) {
            $platform = 'Android';
        } elseif (str_contains($userAgent, 'iPhone')) {
            $platform = 'iPhone';
        } elseif (str_contains($userAgent, 'Linux')) {
            $platform = 'Linux';
        }

        // tên thiết bị
        $deviceName = $platform . ' - ' . $browser;

        // reset phiên hiện tại cũ
        PhienDangNhap::where('nguoi_dung_id', $user->id)
            ->update([
                'la_phien_hien_tai' => false
            ]);

        // tạo token phiên
        $token = Str::random(64);
        
        // kiểm tra thiết bị đã tồn tại chưa
        $existingSession = PhienDangNhap::where('nguoi_dung_id', $user->id)

            ->where('trinh_duyet', $browser)

            ->where('he_dieu_hanh', $platform)

            ->where('dia_chi_ip', $request->ip())

            ->latest('id')

            ->first();


        // nếu đã tồn tại -> update lại
        if ($existingSession) {

            $existingSession->update([

                'token_hash' => hash('sha256', $token),

                'user_agent' => $userAgent,

                'lan_hoat_dong_cuoi' => now(),

                'dang_xuat_luc' => null,

                'la_phien_hien_tai' => true,

                'het_han' => $remember
                    ? now()->addDays(30)
                    : now()->addDay(),
            ]);

        } else {

            // chưa tồn tại -> tạo mới
            PhienDangNhap::create([

                'nguoi_dung_id' => $user->id,

                'token_hash' => hash('sha256', $token),

                'ten_thiet_bi' => $deviceName,

                'trinh_duyet' => $browser,

                'he_dieu_hanh' => $platform,

                'user_agent' => $userAgent,

                'dia_chi_ip' => $request->ip(),

                'lan_hoat_dong_cuoi' => now(),

                'la_phien_hien_tai' => true,

                'het_han' => $remember
                    ? now()->addDays(30)
                    : now()->addDay(),

                'ngay_tao' => now(),
            ]);
        }
        // lưu token vào session
        session([
            'session_token' => $token
        ]);

        \App\Models\NhatKyHoatDong::log($user->id, 'dang_nhap');

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request)
    {
        $token = session('session_token');

        if ($token) {

            PhienDangNhap::where(
                'token_hash',
                hash('sha256', $token)
            )->update([

                'dang_xuat_luc' => now(),

                'la_phien_hien_tai' => false,
            ]);
        }

        if (Auth::check()) {
            \App\Models\NhatKyHoatDong::log(Auth::id(), 'dang_xuat');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Đã đăng xuất thành công.');
    }
}