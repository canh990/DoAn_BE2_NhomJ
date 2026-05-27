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
        $posts = BaiViet::with(['user', 'media', 'originalPost.user', 'originalPost.media', 'poll.options.votes', 'poll.votes'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }, 'bookmarks' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }])
            ->whereIn('loai', ['van_ban', 'hinh_anh', 'chia_se', 'binh_chon'])
            ->where('da_xoa', false)
            ->latest()
            ->take(20)
            ->get();

        return view('components.home', compact('posts'));
    }

    public function explore()
    {
        $media = MediaBaiViet::with('baiViet.user')
            ->whereHas('baiViet', function($query) {
                $query->where('da_xoa', false);
            })
            ->latest('ngay_tao')
            ->paginate(24);

        return view('explore', [
            'media' => $media,
            'title' => __('messages.explore_title'),
            'message' => __('messages.explore_subtitle'),
        ]);
    }

    public function show(BaiViet $post)
    {
        if ($post->da_xoa) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Bài viết đã bị xóa.'], 404);
            }
            return redirect()->route('home')->with('error', 'Bài viết đã bị xóa.');
        }

        $post->load(['user', 'media', 'originalPost.user', 'originalPost.media', 'poll.options.votes', 'poll.votes'])
            ->loadCount(['reactions', 'comments', 'shares'])
            ->load(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }, 'bookmarks' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }]);

        if (request()->ajax()) {
            return view('components.post-card', compact('post'))->render();
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
            'ten_dia_diem' => ['nullable', 'string', 'max:255'],
            'vi_do' => ['nullable', 'numeric'],
            'kinh_do' => ['nullable', 'numeric'],
            'poll_question' => ['nullable', 'string', 'max:500'],
            'poll_options' => ['nullable', 'array', 'min:2', 'max:6'],
            'poll_options.*' => ['nullable', 'string', 'max:255'],
        ]);

        $pollOptions = collect($request->input('poll_options', []))
            ->map(fn ($opt) => trim((string) $opt))
            ->filter(fn ($opt) => $opt !== '')
            ->values();
        $hasPollQuestion = filled(trim((string) $request->input('poll_question', '')));
        $isPoll = $hasPollQuestion || $pollOptions->count() > 0;

        if ($isPoll) {
            $request->validate([
                'poll_question' => ['required', 'string', 'max:500'],
                'poll_options' => ['required', 'array', 'min:2', 'max:6'],
                'poll_options.*' => ['required', 'string', 'max:255'],
            ], [
                'poll_options.min' => 'Cuộc bình chọn cần ít nhất 2 lựa chọn.',
                'poll_options.max' => 'Cuộc bình chọn tối đa 6 lựa chọn.',
            ]);

            $pollOptions = collect($request->input('poll_options', []))
                ->map(fn ($opt) => trim((string) $opt))
                ->filter(fn ($opt) => $opt !== '')
                ->values();

            if ($pollOptions->count() < 2 || $pollOptions->count() > 6) {
                return back()->withErrors(['poll_options' => 'Cuộc bình chọn phải có từ 2 đến 6 lựa chọn.'])->withInput();
            }
        }

        // Kiểm tra ít nhất có nội dung, file, vị trí hoặc poll
        if (
            empty($validated['noi_dung']) &&
            !$request->hasFile('anh') &&
            empty($validated['cam_xuc']) &&
            empty($validated['hoat_dong']) &&
            empty($validated['ten_dia_diem']) &&
            !$isPoll
        ) {
            return back()->withErrors(['noi_dung' => 'Bài viết phải có nội dung, cảm xúc, địa điểm check-in, hình ảnh/video hoặc cuộc bình chọn.']);
        }

        $postType = $request->hasFile('anh') ? 'hinh_anh' : 'van_ban';
        if ($isPoll) {
            $postType = 'binh_chon';
        }

        $post = BaiViet::create([
            'nguoi_dung_id' => auth()->id(),
            'loai' => $postType,
            'noi_dung' => $validated['noi_dung'] ?? null,
            'cam_xuc' => $validated['cam_xuc'] ?? null,
            'hoat_dong' => $validated['hoat_dong'] ?? null,
            'ten_dia_diem' => $validated['ten_dia_diem'] ?? null,
            'vi_do' => $validated['vi_do'] ?? null,
            'kinh_do' => $validated['kinh_do'] ?? null,
            'quyen_rieng_tu' => 'cong_khai',
        ]);

        if ($isPoll) {
            $poll = \App\Models\BinhChon::create([
                'bai_viet_id' => $post->id,
                'cau_hoi' => trim((string) $request->input('poll_question')),
            ]);

            foreach ($pollOptions as $optionText) {
                \App\Models\LuaChonBinhChon::create([
                    'binh_chon_id' => $poll->id,
                    'noi_dung' => $optionText,
                ]);
            }
        }

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

        // --- QUÉT MENTION/TAG VÀ TẠO THÔNG BÁO ---
        $user = auth()->user();
        $mentionService = resolve(\App\Services\MentionService::class);
        $taggedUserIds = $mentionService->processMentions($post->noi_dung ?? '', $user, $post);

        // --- TẠO THÔNG BÁO CHO NGƯỜI THEO DÕI ---
        $followers = $user->followers()
            ->where('trang_thai', 'da_chap_nhan')
            ->whereNotIn('nguoi_dung.id', $taggedUserIds) // Tránh gửi trùng dạng đăng bài nếu đã bị tag
            ->get();
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
        // Xác định bài viết gốc thực sự (nếu bài hiện tại là bài chia sẻ)
        $originalPost = $post->loai === 'chia_se' && $post->bai_goc_id ? BaiViet::find($post->bai_goc_id) : $post;

        if (!$originalPost || $originalPost->da_xoa) {
            return response()->json(['success' => false, 'message' => 'Bài viết gốc không còn tồn tại.'], 404);
        }

        // Kiểm tra xem người dùng đã chia sẻ bài viết này chưa
        $alreadyShared = BaiViet::where('bai_goc_id', $originalPost->id)
            ->where('nguoi_dung_id', auth()->id())
            ->exists();

        if ($alreadyShared) {
            return response()->json(['success' => false, 'message' => 'Bạn đã chia sẻ bài viết này rồi.'], 400);
        }

        // Tạo bài viết mới với loại là chia_se
        $sharedPost = BaiViet::create([
            'nguoi_dung_id' => auth()->id(),
            'loai' => 'chia_se',
            'bai_goc_id' => $originalPost->id,
            'noi_dung' => $request->input('noi_dung', null),
            'quyen_rieng_tu' => 'ban_be',
        ]);

        // Tạo thông báo cho chủ bài viết gốc
        if ($originalPost->nguoi_dung_id !== auth()->id()) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $originalPost->nguoi_dung_id,
                'nguoi_thuc_hien_id' => auth()->id(),
                'loai' => 'chia_se',
                'bai_viet_id' => $originalPost->id,
                'ngay_tao' => now(),
            ]);
        }
        
        // Tạo thông báo cho người mà mình chia sẻ bài của họ (nếu bài hiện tại là bài chia sẻ)
        if ($post->id !== $originalPost->id && $post->nguoi_dung_id !== auth()->id() && $post->nguoi_dung_id !== $originalPost->nguoi_dung_id) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $post->nguoi_dung_id,
                'nguoi_thuc_hien_id' => auth()->id(),
                'loai' => 'chia_se',
                'bai_viet_id' => $originalPost->id,
                'ngay_tao' => now(),
            ]);
        }

        $sharesCount = BaiViet::where('bai_goc_id', $originalPost->id)->count();

        // Render the new post HTML
        $html = view('components.post-card', ['post' => $sharedPost->load(['user', 'media', 'originalPost.user', 'originalPost.media'])])->render();

        return response()->json([
            'success' => true,
            'message' => 'Chia sẻ thành công',
            'shares_count' => $sharesCount,
            'html' => $html,
        ]);
    }

    public function vote(Request $request, \App\Models\BinhChon $poll)
    {
        $validated = $request->validate([
            'lua_chon_id' => ['required', 'integer', 'exists:lua_chon_binh_chon,id'],
        ]);

        $userId = auth()->id();

        $option = \App\Models\LuaChonBinhChon::where('id', $validated['lua_chon_id'])
            ->where('binh_chon_id', $poll->id)
            ->first();

        if (!$option) {
            return response()->json(['success' => false, 'message' => 'Lựa chọn không hợp lệ.'], 422);
        }

        $existingVote = \App\Models\PhieuBau::where('binh_chon_id', $poll->id)
            ->where('nguoi_dung_id', $userId)
            ->first();

        if ($existingVote) {
            $existingVote->lua_chon_id = $option->id;
            $existingVote->save();
        } else {
            \App\Models\PhieuBau::create([
                'binh_chon_id' => $poll->id,
                'nguoi_dung_id' => $userId,
                'lua_chon_id' => $option->id,
            ]);
        }

        $optionsStats = $poll->options()->withCount('votes')->get()->map(function ($opt) {
            return [
                'id' => $opt->id,
                'noi_dung' => $opt->noi_dung,
                'votes_count' => $opt->votes_count,
            ];
        })->values();

        $totalVotes = $poll->votes()->count();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật bình chọn.',
            'options' => $optionsStats,
            'total_votes' => $totalVotes,
            'user_voted_option_id' => $option->id,
        ]);
    }
}
