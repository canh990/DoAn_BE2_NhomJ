<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Bắt buộc phải có dòng này
use Illuminate\Support\Facades\Auth; // Thêm thư viện Auth để xử lý user/logout

class PersonalSettingsController extends Controller
{
    // Hiển thị giao diện Settings
    public function index(Request $request)
    {
        // XỬ LÝ NGÔN NGỮ NGAY TẠI ĐÂY:
        // Đọc session và áp dụng ngôn ngữ trước khi load view
        if (session()->has('personal_locale')) {
            App::setLocale(session('personal_locale'));
        }

        return view('settings');
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

    // Persist language choice (e.g. vi|en)
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

    // Xóa bộ nhớ đệm
    public function clearCache(Request $request)
    {
        // Bạn có thể viết logic thực tế ở đây (VD: clear session tạm, v.v.)
        // Artisan::call('cache:clear');
        
        return back()->with('success', 'Đã xóa bộ nhớ đệm thành công!');
    }

    // Đăng xuất (Dùng tạm nếu project chưa có Route Đăng xuất riêng)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Đẩy về trang chủ hoặc trang đăng nhập
    }

    // Vô hiệu hóa tài khoản
    public function disableAccount(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Logic đánh dấu xóa user của bạn. Ví dụ:
            // $user->update(['ngay_xoa' => now()]);
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        return redirect('/')->with('success', 'Tài khoản đã bị vô hiệu hóa.');
    }
}