<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Hiển thị trang đăng ký
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * postRegister — Xử lý đăng ký tài khoản mới.
     *
     * Luồng:
     * 1. Validate dữ liệu đầu vào
     * 2. Tạo bản ghi mới với da_xac_thuc = false
     * 3. Tạo mã OTP 6 số ngẫu nhiên, lưu vào database
     * 4. Gửi OTP đến email người dùng
     * 5. Đăng nhập tạm thời và redirect đến form xác thực OTP
     *
     * Lưu ý: Bỏ qua bước này nếu login bằng Facebook/Google
     *         (đã mặc định da_xac_thuc = true trong SocialLoginController)
     */
    public function register(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào (Validation)
        $this->validator($request->all())->validate();

        // 2. Tạo người dùng mới với da_xac_thuc = false
        $user = $this->create($request->all());

        // 3. Tạo mã OTP 6 số ngẫu nhiên
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // 4. Lưu OTP vào database (hết hạn sau 10 phút)
        $user->update([
            'otp_code'    => $otpCode,
            'otp_het_han' => Carbon::now()->addMinutes(10),
        ]);

        // 5. Gửi OTP qua email
        Mail::to($user->email)->send(new OtpMail($otpCode, $user->ten_dang_nhap, 'register'));

        // 6. Đăng nhập tạm thời (da_xac_thuc vẫn = false)
        Auth::login($user);

        // 7. Redirect đến trang xác thực OTP
        return redirect()->route('otp.show')
            ->with('email_reset', $user->email);
    }

    /**
     * Hiển thị form xác thực OTP.
     */
    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    /**
     * verifyOtp — Xử lý xác thực mã OTP.
     *
     * - Nếu người dùng nhập đúng mã + chưa hết hạn:
     *   → Cập nhật da_xac_thuc = true (hiển thị tích xanh)
     *   → Xoá OTP khỏi database
     *   → Redirect về trang chủ
     *
     * - Nếu sai hoặc hết hạn:
     *   → Quay lại form với thông báo lỗi
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp'   => ['required', 'array', 'size:6'],
            'otp.*' => ['required', 'digits:1'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.size'     => 'Mã OTP phải có đúng 6 chữ số.',
        ]);

        // Ghép 6 ô input thành chuỗi OTP
        $otpCode = implode('', $request->otp);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
        }

        // Kiểm tra OTP đã hết hạn chưa
        if (!$user->otp_het_han || Carbon::now()->greaterThan($user->otp_het_han)) {
            return redirect()->route('otp.show')
                ->with('email_reset', $user->email)
                ->with('error', 'Mã OTP đã hết hạn. Vui lòng yêu cầu gửi lại.');
        }

        // Kiểm tra mã OTP có đúng không
        if ($otpCode !== $user->otp_code) {
            return redirect()->route('otp.show')
                ->with('email_reset', $user->email)
                ->with('error', 'Mã OTP không chính xác. Vui lòng thử lại.');
        }

        // ✅ Xác thực thành công → da_xac_thuc = true (tích xanh)
        $user->update([
            'da_xac_thuc' => true,
            'otp_code'    => null,
            'otp_het_han' => null,
        ]);

        return redirect()->route('home')
            ->with('success', 'Xác thực email thành công! Chào mừng bạn đến với NHOMJ.');
    }

    /**
     * skipVerification — Bỏ qua xác thực OTP.
     *
     * Redirect về trang chủ ngay lập tức, giữ nguyên da_xac_thuc = false.
     */
    public function skipVerification(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Xoá OTP khỏi database (không cần nữa)
            $user->update([
                'otp_code'    => null,
                'otp_het_han' => null,
            ]);
        }

        // Redirect về trang chủ — da_xac_thuc vẫn = false
        return redirect()->route('home');
    }

    /**
     * Gửi lại mã OTP mới.
     */
    public function resendOtp(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Phiên đăng nhập đã hết hạn.');
        }

        // Tạo OTP mới
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'    => $otpCode,
            'otp_het_han' => Carbon::now()->addMinutes(10),
        ]);

        // Gửi lại email
        Mail::to($user->email)->send(new OtpMail($otpCode, $user->ten_dang_nhap, 'register'));

        return redirect()->route('otp.show')
            ->with('email_reset', $user->email)
            ->with('success', 'Mã OTP mới đã được gửi đến email của bạn.');
    }

    /**
     * Định nghĩa các quy tắc kiểm tra dữ liệu
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'ten_dang_nhap' => [
                'required',
                'min:4',
                'max:30',
                'regex:/^[a-z0-9._]+$/',
                'unique:nguoi_dung,ten_dang_nhap'
            ],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:nguoi_dung,email'],
            'so_dien_thoai' => ['required', 'string', 'max:11', 'unique:nguoi_dung,so_dien_thoai'],
            'mat_khau'      => [
                'required', 
                'string', 
                'min:8',
                // Regex: Ít nhất 1 chữ cái, 1 số, 1 ký tự đặc biệt
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*#?&.])[A-Za-z\d@$!%*#?&.]{8,}$/'
            ],
        ], [
            'ten_dang_nhap.required' => 'Vui lòng nhập tên đăng nhập',
            'ten_dang_nhap.unique'   => 'Tên đăng nhập đã tồn tại',
            'ten_dang_nhap.regex'    => 'Tên đăng nhập chỉ chứa chữ thường, số, dấu chấm và gạch dưới',

            'email.required' => 'Vui lòng nhập email',
            'email.email'    => 'Email không hợp lệ',
            'email.unique'   => 'Email đã tồn tại',

            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại',
            'so_dien_thoai.unique'   => 'Số điện thoại đã tồn tại',

            'mat_khau.required' => 'Vui lòng nhập mật khẩu',
            'mat_khau.min'      => 'Mật khẩu phải có ít nhất 8 ký tự',
            'mat_khau.regex'    => 'Mật khẩu phải bao gồm chữ cái, số và ít nhất một ký tự đặc biệt (!@#$...)',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'ten_dang_nhap' => $data['ten_dang_nhap'],
            'email'         => $data['email'],
            'so_dien_thoai' => $data['so_dien_thoai'],
            'mat_khau_hash' => Hash::make($data['mat_khau']),
            'da_xac_thuc'   => false, // ← Mặc định chưa xác thực
        ]);
    }
}