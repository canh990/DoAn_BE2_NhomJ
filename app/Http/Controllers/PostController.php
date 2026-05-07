<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\MediaBaiViet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class PostController extends Controller
{

  public function index()
{
    $posts = BaiViet::with(['user', 'media']) // THÊM 'media' ở đây
        ->withCount(['reactions', 'comments'])
        ->with(['reactions' => function ($query) {
            $query->where('nguoi_dung_id', auth()->id());
        }, 'comments' => function ($query) {
            $query->with('user')->latest('ngay_tao')->limit(3);
        }])
        // XÓA HOẶC SỬA dòng ->where('loai', 'van_ban')
        ->whereIn('loai', ['van_ban', 'hinh_anh']) // Lấy cả bài chữ và bài ảnh
        ->where('da_xoa', false)
        ->latest()
        ->take(20)
        ->get();

    return view('components.home', compact('posts'));
} 


    public function store(Request $request)
    {
        $validated = $request->validate([
            'noi_dung' => ['nullable', 'string', 'max:280'],
            'anh' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);

        // Kiểm tra ít nhất có nội dung hoặc ảnh
        if (empty($validated['noi_dung']) && !$request->hasFile('anh')) {
            return back()->withErrors(['noi_dung' => 'Bài viết phải có nội dung hoặc hình ảnh.']);
        }

        $post = BaiViet::create([
            'nguoi_dung_id' => auth()->id(),
            'loai' => $request->hasFile('anh') ? 'hinh_anh' : 'van_ban',
            'noi_dung' => $validated['noi_dung'] ?? null,
            'quyen_rieng_tu' => 'cong_khai',
        ]);

        // Xử lý upload ảnh
        if ($request->hasFile('anh')) {
            $file = $request->file('anh');
            $path = $file->store('posts', 'public');

            MediaBaiViet::create([
                'bai_viet_id' => $post->id,
                'loai' => 'hinh_anh',
                'duong_dan' => $path,
                'thu_tu' => 0,
                'ngay_tao' => now(),
            ]);
        }

        return redirect()
            ->route('home')
            ->with('success', 'Bài viết đã được đăng thành công.');
    }
}
