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

        // Lưu OTP vào bảng password_reset_tokens (hoặc bảng riêng)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now()
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
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        // Kiểm tra thời gian hết hạn của OTP (ví dụ: 5 phút)
        $otpExpirationMinutes = 5;
        if ($record && Carbon::parse($record->created_at)->addMinutes($otpExpirationMinutes)->isBefore(Carbon::now())) {
            return back()->with('error', 'Mã OTP đã hết hạn. Vui lòng yêu cầu gửi lại.');
        }

        if (!$record || !Hash::check($otpCode, $record->token)) {
            return back()->with('error', 'Mã OTP không chính xác hoặc đã hết hạn.');
        }

        // OTP đúng, cho phép sang trang đổi mật khẩu
        session(['otp_verified' => true]);
        return redirect()->route('password.reset');
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
                'regex:/[@$!%*#?&]/',   // Ít nhất một ký tự đặc biệt
            ],
        ], [
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.'
        ]);

        $email = session('email_reset');
        $user = User::where('email', $email)->first();

        if ($user) {
            // Đổi 'password' thành 'mat_khau_hash' để khớp với bảng nguoi_dung
            $user->update(['mat_khau_hash' => Hash::make($request->password)]);

            // Xóa token và session sau khi xong
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            session()->forget(['email_reset', 'otp_verified']);

            return redirect()->route('login')->with('success', 'Mật khẩu đã được cập nhật thành công!');
        }

        return redirect()->route('password.request')->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
    }
}
