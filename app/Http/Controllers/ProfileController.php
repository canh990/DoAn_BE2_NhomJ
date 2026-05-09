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
                $query->with('user')->latest('ngay_tao')->limit(3);
            }])
            ->whereIn('loai', ['van_ban', 'hinh_anh'])
            ->where('da_xoa', false)
            ->latest()
            ->take(20)
            ->get();

        return view('profile.profile', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function showByUsername(string $username)
    {
        $user = User::query()
            ->where('ten_dang_nhap', $username)
            ->withCount(['followers', 'following'])
            ->firstOrFail();

        $posts = $user->posts()
            ->with(['user', 'media'])
            ->withCount(['reactions', 'comments'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->with('user')->latest('ngay_tao')->limit(3);
            }])
            ->whereIn('loai', ['van_ban', 'hinh_anh'])
            ->where('da_xoa', false)
            ->latest()
            ->take(20)
            ->get();

        return view('profile.profile', [
            'user' => $user,
            'posts' => $posts,
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

        $status = $me->following()->toggle($user->id);
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
            ->withCount(['followers', 'following'])
            ->firstOrFail();

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
            ->withCount(['followers', 'following'])
            ->firstOrFail();

        $connections = $user->following()->paginate(20);

        return view('profile.connections', [
            'user' => $user,
            'connections' => $connections,
            'type' => 'following',
        ]);
    }
}
