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

        // Chuẩn bị dữ liệu cho Popup Giới thiệu (About) từ Database
        $aboutInfo = [
            'app_name' => \DB::table('tro_giup')->where('loai', 'info')->where('khoa', 'app_name')->value('tra_loi') ?? 'Hệ thống Quản trị Nội bộ',
            'version' => \DB::table('tro_giup')->where('loai', 'info')->where('khoa', 'version')->value('tra_loi') ?? 'v1.2.4',
            'release_date' => \DB::table('tro_giup')->where('loai', 'info')->where('khoa', 'release_date')->value('tra_loi') ?? '22/05/2026',
            'company' => \DB::table('tro_giup')->where('loai', 'info')->where('khoa', 'company')->value('tra_loi') ?? 'Công ty TNHH Giải pháp Số',
        ];

        // Chuẩn bị dữ liệu cho Popup Hỗ trợ (Support) từ Database
        $supportInfo = [
            'email' => \DB::table('tro_giup')->where('loai', 'info')->where('khoa', 'email')->value('tra_loi') ?? 'it-support@company.com',
            'hotline' => \DB::table('tro_giup')->where('loai', 'info')->where('khoa', 'hotline')->value('tra_loi') ?? '1900 1234',
            'zalo_group' => \DB::table('tro_giup')->where('loai', 'info')->where('khoa', 'zalo_group')->value('tra_loi') ?? 'https://zalo.me/g/nhomhotroIT',
        ];

        // Lấy danh sách FAQ tương ứng với ngôn ngữ hiện tại từ Database
        $locale = app()->getLocale();
        $faqs = \DB::table('tro_giup')
            ->where('loai', 'faq')
            ->where(function($query) use ($locale) {
                $query->where('ngon_ngu', $locale)
                      ->orWhere('ngon_ngu', 'all');
            })
            ->get();

        // Lấy dung lượng bộ nhớ đệm hiện tại của người dùng
        $dungLuongCache = \DB::table('cai_dat_nguoi_dung')
            ->where('nguoi_dung_id', auth()->id())
            ->value('dung_luong_cache') ?? 0.0;

        return view('settings', compact('sessions', 'aboutInfo', 'supportInfo', 'faqs', 'dungLuongCache'));
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
            'locale' => 'required|string|in:vi,en'
        ]);

        session(['personal_locale' => $data['locale']]);

        return response()->json(['status' => 'ok', 'locale' => $data['locale']]);
    }

    // ==========================================
    // CÁC HÀM XỬ LÝ NÚT BẤM DƯỚI GIAO DIỆN
    // ==========================================

    public function clearCache(Request $request)
    {
        \DB::table('cai_dat_nguoi_dung')
            ->where('nguoi_dung_id', auth()->id())
            ->update(['dung_luong_cache' => 0.0]);

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