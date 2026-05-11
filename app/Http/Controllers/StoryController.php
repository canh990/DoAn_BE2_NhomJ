<?php

namespace App\Http\Controllers;

use App\Models\Tin24h;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    /** Trang đăng story */
    public function create()
    {
        return view('components.story-create');
    }

    /** Lưu story mới vào DB */
    public function store(Request $request)
    {
        $request->validate([
            'media' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov', 'max:102400'], // 100MB
        ]);

        $file = $request->file('media');
        $path = $file->store('stories', 'public');

        $mimeType  = $file->getMimeType();
        $loaiMedia = str_starts_with($mimeType, 'video/') ? 'video' : 'hinh_anh';

        $story = Tin24h::create([
            'nguoi_dung_id'  => auth()->id(),
            'duong_dan_media' => $path,
            'loai_media'     => $loaiMedia,
            'quyen_rieng_tu' => $request->input('quyen_rieng_tu', 'cong_khai'),
            'het_han'        => now()->addHours(24),
            'ngay_tao'       => now(),
        ]);

        // --- TẠO THÔNG BÁO CHO NGƯỜI THEO DÕI ---
        $user = auth()->user();
        $followers = $user->followers()->where('trang_thai', 'da_chap_nhan')->get();
        foreach ($followers as $follower) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $follower->id,
                'nguoi_thuc_hien_id' => $user->id,
                'loai' => 'dang_tin',
                'ngay_tao' => now(),
            ]);
        }
        // ---------------------------------------

        return redirect()->route('home')->with('success', 'Tin đã được chia sẻ!');
    }

    /** Xóa story (chỉ tác giả) */
    public function destroy(Tin24h $story)
    {
        if ($story->nguoi_dung_id !== auth()->id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($story->duong_dan_media)) {
            Storage::disk('public')->delete($story->duong_dan_media);
        }

        $story->delete();

        return back()->with('success', 'Đã xóa tin.');
    }
}
