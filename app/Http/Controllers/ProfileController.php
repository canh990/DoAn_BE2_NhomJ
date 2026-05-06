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
            'ngay_sinh' => ['nullable', 'date', 'before_or_equal:today'],
            'noi_o' => ['nullable', 'string', 'max:255'],
            'quyen_rieng_tu' => ['required', Rule::in(['cong_khai', 'ban_be', 'rieng_tu'])],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'anh_bia' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('anh_dai_dien')) {
            if ($user->anh_dai_dien) {
                Storage::disk('public')->delete($user->anh_dai_dien);
            }

            $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('avatars', 'public');
        }

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
        
        return response()->json([
            'is_following' => count($status['attached']) > 0,
            'followers_count' => $user->followers()->count()
        ]);
    }
}
