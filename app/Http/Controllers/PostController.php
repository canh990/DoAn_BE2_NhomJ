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
    $posts = BaiViet::with(['user', 'media', 'originalPost.user', 'originalPost.media']) // THÊM 'media' ở đây
        ->withCount(['reactions', 'comments', 'shares'])
        ->with(['reactions' => function ($query) {
            $query->where('nguoi_dung_id', auth()->id());
        }, 'comments' => function ($query) {
            $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
        }])
        // XÓA HOẶC SỬA dòng ->where('loai', 'van_ban')
        ->whereIn('loai', ['van_ban', 'hinh_anh', 'chia_se']) // Lấy cả bài chữ và bài ảnh
        ->where('da_xoa', false)
        ->latest()
        ->take(20)
        ->get();

    return view('components.home', compact('posts'));
} 

    public function show(BaiViet $post)
    {
        $post->load(['user', 'media', 'originalPost.user', 'originalPost.media'])
            ->loadCount(['reactions', 'comments', 'shares'])
            ->load(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }]);

        if ($post->da_xoa) {
            abort(404);
        }

        return view('posts.show', compact('post'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'noi_dung' => ['nullable', 'string', 'max:2000'],
            'cam_xuc' => ['nullable', 'string', 'max:100'],
            'hoat_dong' => ['nullable', 'string', 'max:100'],
            'anh' => ['nullable', 'array', 'max:10'], // Tối đa 10 tệp
            'anh.*' => ['file', 'mimes:jpeg,png,jpg,gif,webp,bmp,svg,heic,heif,mp4,mov,webm,avi,mkv,wmv', 'max:51200'], 
        ]);

        // Kiểm tra ít nhất có nội dung hoặc file
        if (empty($validated['noi_dung']) && !$request->hasFile('anh') && empty($validated['cam_xuc']) && empty($validated['hoat_dong'])) {
            return back()->withErrors(['noi_dung' => 'Bài viết phải có nội dung, cảm xúc, hoặc hình ảnh/video.']);
        }

        $post = BaiViet::create([
            'nguoi_dung_id' => auth()->id(),
            'loai' => $request->hasFile('anh') ? 'hinh_anh' : 'van_ban',
            'noi_dung' => $validated['noi_dung'] ?? null,
            'cam_xuc' => $validated['cam_xuc'] ?? null,
            'hoat_dong' => $validated['hoat_dong'] ?? null,
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

        // --- TẠO THÔNG BÁO CHO NGƯỜI THEO DÕI ---
        $user = auth()->user();
        $followers = $user->followers()->where('trang_thai', 'da_chap_nhan')->get();
        foreach ($followers as $follower) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $follower->id,
                'nguoi_thuc_hien_id' => $user->id,
                'loai' => 'dang_bai',
                'bai_viet_id' => $post->id,
                'ngay_tao' => now(),
            ]);
        }
        // ---------------------------------------

        return redirect()
            ->route('home')
            ->with('success', 'Bài viết đã được đăng thành công.');
    }

    public function update(Request $request, BaiViet $post)
    {
        // Kiểm tra quyền (chỉ tác giả mới được sửa)
        if ($post->nguoi_dung_id !== auth()->id()) {
            return back()->withErrors(['error' => 'Bạn không có quyền chỉnh sửa bài viết này.']);
        }

        $validated = $request->validate([
            'noi_dung' => ['required', 'string', 'max:280'],
        ]);

        $post->update([
            'noi_dung' => $validated['noi_dung'],
            'da_chinh_sua' => true,
        ]);

        return back()->with('success', 'Bài viết đã được chỉnh sửa thành công.');
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

    public function share(Request $request, BaiViet $post)
    {
        // Kiểm tra xem bài gốc có phải là bài đã xóa không
        if ($post->da_xoa) {
            return response()->json(['success' => false, 'message' => 'Bài viết gốc không còn tồn tại.'], 404);
        }

        // Kiểm tra xem người dùng đã chia sẻ bài viết này chưa
        $alreadyShared = BaiViet::where('bai_goc_id', $post->id)
            ->where('nguoi_dung_id', auth()->id())
            ->exists();

        if ($alreadyShared) {
            return response()->json(['success' => false, 'message' => 'Bạn đã chia sẻ bài viết này rồi.'], 400);
        }

        // Tạo bài viết mới với loại là chia_se
        $sharedPost = BaiViet::create([
            'nguoi_dung_id' => auth()->id(),
            'loai' => 'chia_se',
            'bai_goc_id' => $post->id,
            'noi_dung' => $request->input('noi_dung', null),
            'quyen_rieng_tu' => 'ban_be',
        ]);

        // Tạo thông báo cho chủ bài viết gốc
        if ($post->nguoi_dung_id !== auth()->id()) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $post->nguoi_dung_id,
                'nguoi_thuc_hien_id' => auth()->id(),
                'loai' => 'chia_se',
                'bai_viet_id' => $post->id,
                'ngay_tao' => now(),
            ]);
        }

        $sharesCount = BaiViet::where('bai_goc_id', $post->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Chia sẻ bài viết thành công!',
            'shares_count' => $sharesCount,
        ]);
    }
}
