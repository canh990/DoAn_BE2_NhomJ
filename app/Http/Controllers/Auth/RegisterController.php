<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
     * Xử lý lưu trữ người dùng mới
     */
    public function register(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào (Validation)
        $this->validator($request->all())->validate();

        // 2. Tạo người dùng mới
        $user = $this->create($request->all());

        // // 3. Đăng nhập ngay sau khi đăng ký thành công
        // Auth::login($user);

        // 4. Chuyển hướng về trang chủ hoặc dashboard
       // return redirect()->intended('/home')->with('success', 'Chào mừng bạn đã gia nhập NHOMJ!');
       return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập để tiếp tục.');
    }

    /**
     * Định nghĩa các quy tắc kiểm tra dữ liệu
     */
    // app/Http/Controllers/Auth/RegisterController.php

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
        'so_dien_thoai' => ['required', 'string', 'max:20', 'unique:nguoi_dung,so_dien_thoai'],
        'mat_khau'      => [
            'required', 
            'string', 
            'min:8',
            // Regex: Ít nhất 1 chữ cái, 1 số, 1 ký tự đặc biệt
            //'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
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
        'mat_khau_hash' => Hash::make($data['mat_khau']), // Chú ý: mat_khau_hash
    ]);
}
}