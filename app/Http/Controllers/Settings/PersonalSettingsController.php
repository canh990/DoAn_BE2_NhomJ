<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Models\PhienDangNhap;

class PersonalSettingsController extends Controller
{
    // No constructor: layout will apply session locale during view rendering.

    // Hiển thị giao diện Settings
    public function index(Request $request)
    {
        // XỬ LÝ NGÔN NGỮ NGAY TẠI ĐÂY:
        // Đọc session và áp dụng ngôn ngữ trước khi load view
        if (session()->has('personal_locale')) {
            App::setLocale(session('personal_locale'));
        }

        // lấy danh sách phiên đăng nhập
        $sessions = PhienDangNhap::where('nguoi_dung_id', auth()->id())
            ->whereNull('dang_xuat_luc')
            ->latest('lan_hoat_dong_cuoi')
            ->get();

        // Chuẩn bị dữ liệu cho Popup Giới thiệu (About)
        $aboutInfo = [
            'app_name' => 'Hệ thống Quản trị Nội bộ',
            'version' => 'v1.2.4',
            'release_date' => '22/05/2026',
            'company' => 'Công ty TNHH Giải pháp Số',
        ];

        // Chuẩn bị dữ liệu cho Popup Hỗ trợ (Support)
        $supportInfo = [
            'email' => 'it-support@company.com',
            'hotline' => '1900 1234',
            'zalo_group' => 'https://zalo.me/g/nhomhotroIT',
        ];

        return view('settings', compact('sessions', 'aboutInfo', 'supportInfo'));
    }

    // Persist theme choice (light|dark) into session
    public function setTheme(Request $request)
    {
        $data = $request->validate([
            'theme' => 'required|string|in:light,dark'
        ]);

        session(['personal_theme' => $data['theme']]);

        return response()->json(['status' => 'ok', 'theme' => $data['theme']]);
    }

    // Persist language choice (e.g. vi|en|ja)
    public function setLanguage(Request $request)
    {
        $data = $request->validate([
            'locale' => 'required|string|in:vi,en,ja'
        ]);

        session(['personal_locale' => $data['locale']]);

        return response()->json(['status' => 'ok', 'locale' => $data['locale']]);
    }

    // ==========================================
    // CÁC HÀM XỬ LÝ NÚT BẤM DƯỚI GIAO DIỆN
    // ==========================================

    public function clearCache(Request $request)
    {
        return back()->with('success', 'Đã xóa bộ nhớ đệm thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); 
    }

    public function disableAccount(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        return redirect('/')->with('success', 'Tài khoản đã bị vô hiệu hóa.');
    }

    public function logoutSession($id)
    {
        $session = PhienDangNhap::where('nguoi_dung_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        // Đánh dấu đã đăng xuất
        $session->update([
            'dang_xuat_luc' => now(),
            'la_phien_hien_tai' => false,
        ]);

        return back()->with('success', 'Đã đăng xuất thiết bị thành công!');
    }
}