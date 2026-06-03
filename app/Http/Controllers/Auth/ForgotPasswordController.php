<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\OtpMail;

class ForgotPasswordController extends Controller
{
    // BƯỚC 1: Hiển thị trang nhập Email
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // Xử lý gửi OTP
    public function sendOtp(Request $request)
    {
        // Đổi 'exists:users,email' thành 'exists:nguoi_dung,email'
        $request->validate(['email' => 'required|email|exists:nguoi_dung,email'], [
            'email.exists' => 'Email này không tồn tại trong hệ thống.'
        ]);

        $otp = rand(100000, 999999);
        $email = $request->email;

        // Lấy thông tin user để gửi tên vào email
        $user = User::where('email', $email)->first();
        $userName = $user ? $user->ten_dang_nhap : 'Người dùng';

        // Lưu OTP vào bảng dat_lai_mat_khau
        DB::table('dat_lai_mat_khau')->updateOrInsert(
            ['nguoi_dung_id' => $user->id],
            [
                'ma_otp' => $otp,
                'het_han' => Carbon::now()->addMinutes(10),
                'da_su_dung' => false,
                'ngay_tao' => Carbon::now()
            ]
        );

        // Gửi Mail
        Mail::to($email)->send(new OtpMail($otp, $userName));

        // Tạm thời log ra để test nếu chưa có Mail server
        \Log::info("OTP cho $email là: $otp");

        // Lưu email vào session để dùng cho bước sau
        session(['email_reset' => $email]);

        return redirect()->route('password.otp.show')->with('success', 'Mã OTP đã được gửi vào Email của bạn.');
    }

    // BƯỚC 2: Hiển thị trang nhập OTP
    public function showOtpForm()
    {
        if (!session('email_reset')) return redirect()->route('password.request');
        return view('auth.verify-otp');
    }

    // Xác thực OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|array|min:6']);
        $otpCode = implode('', $request->otp); // Ghép mảng 6 ô input thành chuỗi

        $email = session('email_reset');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->with('error', 'Không tìm thấy người dùng.');
        }

        $record = DB::table('dat_lai_mat_khau')->where('nguoi_dung_id', $user->id)->first();

        if (!$record || $record->da_su_dung) {
            return back()->with('error', 'Mã OTP không chính xác hoặc đã được sử dụng.');
        }

        if (Carbon::now()->isAfter($record->het_han)) {
            return back()->with('error', 'Mã OTP đã hết hạn. Vui lòng yêu cầu gửi lại.');
        }

        if ($otpCode !== $record->ma_otp) {
            return back()->with('error', 'Mã OTP không chính xác.');
        }

        // OTP đúng, cho phép sang trang đổi mật khẩu
        session(['otp_verified' => true]);
        return redirect()->route('password.reset')->with('success', 'Xác thực OTP thành công. Vui lòng đặt lại mật khẩu mới.');
    }

    // BƯỚC 3: Hiển thị trang đặt lại mật khẩu
    public function showResetForm()
    {
        if (!session('otp_verified')) return redirect()->route('password.request');
        return view('auth.reset-password');
    }

    // Cập nhật mật khẩu mới
    public function reset(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-zA-Z]/',      // Ít nhất một chữ cái
                'regex:/[0-9]/',        // Ít nhất một số
                'regex:/[@$!%*#?&.]/',   // Ít nhất một ký tự đặc biệt
            ],
        ], [
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.'
        ]);

        $email = session('email_reset');
        $user = User::where('email', $email)->first();

        if ($user) {
            // Đổi 'password' thành 'mat_khau_hash' để khớp với bảng nguoi_dung
            $user->update(['mat_khau_hash' => Hash::make($request->password)]);

            // Đánh dấu OTP đã sử dụng và xóa session sau khi xong
            DB::table('dat_lai_mat_khau')->where('nguoi_dung_id', $user->id)->update(['da_su_dung' => true]);
            session()->forget(['email_reset', 'otp_verified']);

            return redirect()->route('login')->with('success', 'Mật khẩu đã được cập nhật thành công!');
        }

        return redirect()->route('password.request')->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
    }
}
