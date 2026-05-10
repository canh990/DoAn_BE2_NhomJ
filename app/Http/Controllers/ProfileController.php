<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->loadCount(['followers', 'following']);

        $posts = $user->posts()
            ->with(['user', 'media'])
            ->withCount(['reactions', 'comments'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }])
            ->whereIn('loai', ['van_ban', 'hinh_anh'])
            ->where('da_xoa', false)
            ->latest()
            ->take(20)
            ->get();

        $stories = \App\Models\Tin24h::with('user')
            ->where('nguoi_dung_id', $user->id)
            ->conHan()
            ->latest('ngay_tao')
            ->get();

        return view('profile.profile', [
            'user' => $user,
            'posts' => $posts,
            'stories' => $stories,
        ]);
    }

    public function showByUsername(string $username)
    {
        $user = User::query()
            ->where('ten_dang_nhap', $username)
            ->where('con_hoat_dong', true)
            ->withCount(['followers', 'following'])
            ->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Không tìm thấy tài khoản người dùng.');
        }

        $posts = $user->posts()
            ->with(['user', 'media'])
            ->withCount(['reactions', 'comments'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }])
            ->whereIn('loai', ['van_ban', 'hinh_anh'])
            ->where('da_xoa', false)
            ->latest()
            ->take(20)
            ->get();

        $stories = \App\Models\Tin24h::with('user')
            ->where('nguoi_dung_id', $user->id)
            ->conHan()
            ->latest('ngay_tao')
            ->get();

        return view('profile.profile', [
            'user' => $user,
            'posts' => $posts,
            'stories' => $stories,
        ]);
    }

    public function edit()
    {
        return view('profile.profile-edit', [
            'user' => auth()->user(),
        ]);
    }

  public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'ten_dang_nhap' => [
                'required',
                'string',
                'max:50',
                Rule::unique('nguoi_dung', 'ten_dang_nhap')->ignore($user->id),
            ],
            'so_dien_thoai' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('nguoi_dung', 'so_dien_thoai')->ignore($user->id),
            ],
            'tieu_su' => ['nullable', 'string', 'max:1000'],
            'ngay_sinh' => ['nullable', 'date', 'before_or_equal:today'], // Quy tắc validate
            'noi_o' => ['nullable', 'string', 'max:255'],
            'quyen_rieng_tu' => ['required', Rule::in(['cong_khai', 'ban_be', 'rieng_tu'])],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'anh_bia' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            // Tin nhắn lỗi tiếng Việt tương ứng
            'ngay_sinh.date' => 'Ngày sinh không đúng định dạng ngày tháng.',
            'ngay_sinh.before_or_equal' => 'Ngày,Tháng,Năm sinh không thể lớn hơn hiện tại.',
            'ten_dang_nhap.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'so_dien_thoai.unique' => 'Số điện thoại này đã được sử dụng.',
        ]);

        // Xử lý ảnh đại diện
        if ($request->hasFile('anh_dai_dien')) {
            if ($user->anh_dai_dien) {
                Storage::disk('public')->delete($user->anh_dai_dien);
            }
            $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('avatars', 'public');
        }

        // Xử lý ảnh bìa
        if ($request->hasFile('anh_bia')) {
            if ($user->anh_bia) {
                Storage::disk('public')->delete($user->anh_bia);
            }
            $validated['anh_bia'] = $request->file('anh_bia')->store('covers', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('profile')
            ->with('success', 'Đã cập nhật hồ sơ thành công.');
    }

    public function toggleFollow(User $user)
    {
        $me = auth()->user();
        if ($me->id === $user->id) {
            return response()->json(['message' => 'Bạn không thể tự theo dõi chính mình.'], 400);
        }

        $trangThai = $user->quyen_rieng_tu === 'rieng_tu' ? 'cho_chap_nhan' : 'da_chap_nhan';
        
        $status = $me->following()->toggle([
            $user->id => [
                'trang_thai' => $trangThai,
                'ngay_tao' => now(),
            ]
        ]);
        
        $isFollowing = count($status['attached']) > 0;

        if ($isFollowing) {
            // Tạo thông báo cho người được theo dõi
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $user->id,
                'nguoi_thuc_hien_id' => $me->id,
                'loai' => 'theo_doi',
                'ngay_tao' => now(),
            ]);
        } else {
            // Xóa thông báo nếu bỏ theo dõi
            \App\Models\ThongBao::where([
                'nguoi_dung_id' => $user->id,
                'nguoi_thuc_hien_id' => $me->id,
                'loai' => 'theo_doi',
            ])->delete();
        }

        return response()->json([
            'is_following' => $isFollowing,
            'followers_count' => $user->followers()->count()
        ]);
    }

    public function followers(string $username)
    {
        $user = User::query()
            ->where('ten_dang_nhap', $username)
            ->where('con_hoat_dong', true)
            ->withCount(['followers', 'following'])
            ->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Không tìm thấy tài khoản người dùng.');
        }

        $connections = $user->followers()->paginate(20);

        return view('profile.connections', [
            'user' => $user,
            'connections' => $connections,
            'type' => 'followers',
        ]);
    }

    public function following(string $username)
    {
        $user = User::query()
            ->where('ten_dang_nhap', $username)
            ->where('con_hoat_dong', true)
            ->withCount(['followers', 'following'])
            ->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Không tìm thấy tài khoản người dùng.');
        }

        $connections = $user->following()->paginate(20);

        return view('profile.connections', [
            'user' => $user,
            'connections' => $connections,
            'type' => 'following',
        ]);
    }

    public function sendActionOtp(Request $request)
    {
        $user = auth()->user();

        if (empty($user->nha_cung_cap_oauth)) {
            return response()->json(['success' => false, 'message' => 'Tài khoản này không sử dụng OTP cho chức năng này.']);
        }

        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'    => $otpCode,
            'otp_het_han' => \Carbon\Carbon::now()->addMinutes(5),
        ]);

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otpCode, $user->ten_dang_nhap, 'account_action'));

        return response()->json(['success' => true, 'message' => 'Mã OTP đã được gửi đến email của bạn.']);
    }

    public function deactivate(Request $request)
    {
        $user = auth()->user();

        if (empty($user->nha_cung_cap_oauth)) {
            $request->validate([
                'password_deactivate' => ['required', 'string'],
            ], [
                'password_deactivate.required' => 'Vui lòng nhập mật khẩu.',
            ]);

            if (!\Illuminate\Support\Facades\Hash::check($request->password_deactivate, $user->mat_khau_hash)) {
                return back()->withErrors(['password_deactivate' => 'Mật khẩu không chính xác.']);
            }
        } else {
            $request->validate([
                'otp_deactivate' => ['required', 'string', 'size:6'],
            ], [
                'otp_deactivate.required' => 'Vui lòng nhập mã OTP.',
                'otp_deactivate.size' => 'Mã OTP phải có 6 chữ số.',
            ]);

            if ($request->otp_deactivate !== $user->otp_code || !$user->otp_het_han || \Carbon\Carbon::now()->greaterThan($user->otp_het_han)) {
                return back()->withErrors(['otp_deactivate' => 'Mã OTP không hợp lệ hoặc đã hết hạn.']);
            }

            // Xóa OTP sau khi dùng
            $user->update(['otp_code' => null, 'otp_het_han' => null]);
        }

        // Chỉ ẩn (tạm khóa) người dùng bằng cách set con_hoat_dong = false
        $user->update(['con_hoat_dong' => false]);

        auth()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Tài khoản của bạn đã được vô hiệu hóa. Bạn có thể đăng nhập lại để khôi phục.');
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();

        if (empty($user->nha_cung_cap_oauth)) {
            $request->validate([
                'password_delete' => ['required', 'string'],
            ], [
                'password_delete.required' => 'Vui lòng nhập mật khẩu để xác nhận.',
            ]);

            if (!\Illuminate\Support\Facades\Hash::check($request->password_delete, $user->mat_khau_hash)) {
                return back()->withErrors(['password_delete' => 'Mật khẩu không chính xác.']);
            }
        } else {
            $request->validate([
                'otp_delete' => ['required', 'string', 'size:6'],
            ], [
                'otp_delete.required' => 'Vui lòng nhập mã OTP.',
                'otp_delete.size' => 'Mã OTP phải có 6 chữ số.',
            ]);

            if ($request->otp_delete !== $user->otp_code || !$user->otp_het_han || \Carbon\Carbon::now()->greaterThan($user->otp_het_han)) {
                return back()->withErrors(['otp_delete' => 'Mã OTP không hợp lệ hoặc đã hết hạn.']);
            }

            // Xóa OTP sau khi dùng
            $user->update(['otp_code' => null, 'otp_het_han' => null]);
        }

        auth()->logout();
        
        // Permanent delete
        $user->forceDelete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Tài khoản của bạn đã được xóa vĩnh viễn khỏi hệ thống.');
    }
}
