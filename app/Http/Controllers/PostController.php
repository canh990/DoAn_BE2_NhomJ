<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = BaiViet::with('user')
            ->withCount(['reactions', 'comments'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->with('user')->latest('ngay_tao')->limit(3);
            }])
            ->where('loai', 'van_ban')
            ->where('da_xoa', false)
            ->latest()
            ->take(20)
            ->get();

        return view('components.home', [
            'posts' => $posts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'noi_dung' => ['required', 'string', 'max:280'],
        ]);

        BaiViet::create([
            'nguoi_dung_id' => auth()->id(),
            'loai' => 'van_ban',
            'noi_dung' => $validated['noi_dung'],
            'quyen_rieng_tu' => 'cong_khai',
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'Bài viết đã được đăng thành công.');
    }
}
