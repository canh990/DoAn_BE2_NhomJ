<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\MediaBaiViet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
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
