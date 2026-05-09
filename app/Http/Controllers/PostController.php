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
            'anh' => ['nullable', 'array', 'max:10'], // Tối đa 10 tệp
            'anh.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov', 'max:512000'], // max 500MB
        ]);

        // Kiểm tra ít nhất có nội dung hoặc file
        if (empty($validated['noi_dung']) && !$request->hasFile('anh')) {
            return back()->withErrors(['noi_dung' => 'Bài viết phải có nội dung hoặc hình ảnh/video.']);
        }

        $post = BaiViet::create([
            'nguoi_dung_id' => auth()->id(),
            'loai' => $request->hasFile('anh') ? 'hinh_anh' : 'van_ban',
            'noi_dung' => $validated['noi_dung'] ?? null,
            'quyen_rieng_tu' => 'cong_khai',
        ]);

        // Xử lý upload ảnh/video
        if ($request->hasFile('anh')) {
            foreach ($request->file('anh') as $index => $file) {
                $path = $file->store('posts', 'public');
                $mimeType = $file->getMimeType();
                $loaiMedia = str_starts_with($mimeType, 'video/') ? 'video' : 'hinh_anh';

                MediaBaiViet::create([
                    'bai_viet_id' => $post->id,
                    'loai' => $loaiMedia,
                    'duong_dan' => $path,
                    'thu_tu' => $index,
                    'ngay_tao' => now(),
                ]);
            }
        }

        return redirect()
            ->route('home')
            ->with('success', 'Bài viết đã được đăng thành công.');
    }

    public function destroy(BaiViet $post)
    {
        // Kiểm tra quyền xóa (chỉ tác giả mới được xóa)
        if ($post->nguoi_dung_id !== auth()->id()) {
            return back()->withErrors(['error' => 'Bạn không có quyền xóa bài viết này.']);
        }

        // Xóa các file media trong storage để giải phóng bộ nhớ
        if ($post->media) {
            foreach ($post->media as $media) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($media->duong_dan)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($media->duong_dan);
                }
                $media->delete(); // Xóa khỏi DB để sạch sẽ
            }
        }

        // Đánh dấu bài viết đã xóa (Sử dụng gán trực tiếp để bỏ qua giới hạn fillable)
        $post->da_xoa = true;
        $post->save();
        
        return back()->with('success', 'Bài viết đã được xóa thành công.');
    }
}
