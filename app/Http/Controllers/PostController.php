<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\MediaBaiViet;
use App\Models\Hashtag;
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

    public function explore(Request $request)
    {
        $type = $request->input('type', 'all');
        $time = $request->input('time', 'all');
        $sort = $request->input('sort', 'latest');
        $keyword = trim($request->input('search', ''));

        // Start base query on BaiViet model
        $query = BaiViet::with(['user', 'media', 'originalPost.user', 'originalPost.media', 'poll.options.votes', 'poll.votes'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with(['reactions' => function ($q) {
                $q->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($q) {
                $q->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }, 'bookmarks' => function ($q) {
                $q->where('nguoi_dung_id', auth()->id());
            }])
            ->where('da_xoa', false);

        $user = auth()->user();
        $isRecommendation = false;
        $recommendedTagNames = [];

        // Apply filters based on search keyword
        if ($keyword) {
            if ($type === 'hashtag') {
                $cleanKeyword = ltrim($keyword, '#');
                $query->whereHas('hashtags', function($q) use ($cleanKeyword) {
                    $q->where('ten', 'LIKE', "%{$cleanKeyword}%");
                });
            } elseif ($type === 'post') {
                if (str_starts_with($keyword, '#')) {
                    $cleanKeyword = ltrim($keyword, '#');
                    $query->where(function($q) use ($keyword, $cleanKeyword) {
                        $q->where('noi_dung', 'LIKE', "%{$keyword}%")
                          ->orWhereHas('hashtags', function($qh) use ($cleanKeyword) {
                              $qh->where('ten', 'LIKE', "%{$cleanKeyword}%");
                          });
                    });
                } else {
                    $query->where('noi_dung', 'LIKE', "%{$keyword}%");
                }
            } elseif ($type === 'user') {
                $query->whereHas('user', function($q) use ($keyword) {
                    $q->where('ten_dang_nhap', 'LIKE', "%{$keyword}%")
                      ->orWhere('email', 'LIKE', "%{$keyword}%");
                });
            } else { // type === 'all'
                if (str_starts_with($keyword, '#')) {
                    $cleanKeyword = ltrim($keyword, '#');
                    $query->where(function($q) use ($keyword, $cleanKeyword) {
                        $q->where('noi_dung', 'LIKE', "%{$keyword}%")
                          ->orWhereHas('hashtags', function($qh) use ($cleanKeyword) {
                              $qh->where('ten', 'LIKE', "%{$cleanKeyword}%");
                          });
                    });
                } else {
                    $query->where(function($q) use ($keyword) {
                        $q->where('noi_dung', 'LIKE', "%{$keyword}%")
                          ->orWhereHas('hashtags', function($qh) use ($keyword) {
                              $qh->where('ten', 'LIKE', "%{$keyword}%");
                          })
                          ->orWhereHas('user', function($qu) use ($keyword) {
                              $qu->where('ten_dang_nhap', 'LIKE', "%{$keyword}%")
                                ->orWhere('email', 'LIKE', "%{$keyword}%");
                          });
                    });
                }
            }
        } else {
            // KHÔNG CÓ TỪ KHÓA TÌM KIẾM -> Áp dụng thuật toán Đề xuất bài viết thịnh hành từ người lạ dựa trên sở thích
            $isRecommendation = true;

            // 1. Chỉ lấy bài viết của NGƯỜI LẠ (chưa từng theo dõi và không phải là chính mình)
            $followingIds = [];
            if ($user) {
                $followingIds = \DB::table('theo_doi')
                    ->where('nguoi_theo_doi_id', $user->id)
                    ->pluck('nguoi_duoc_theo_doi_id')
                    ->toArray();
            }
            $excludeUserIds = array_unique(array_merge($user ? [$user->id] : [], $followingIds));
            $query->whereNotIn('nguoi_dung_id', $excludeUserIds);

            // 2. Dựa trên sở thích (Hashtags quan tâm nhiều nhất)
            $preferredHashtags = [];
            if ($user) {
                $likedPostIds = \DB::table('cam_xuc')
                    ->where('nguoi_dung_id', $user->id)
                    ->pluck('bai_viet_id')
                    ->toArray();

                $bookmarkedPostIds = \DB::table('bai_viet_da_luu')
                    ->where('nguoi_dung_id', $user->id)
                    ->pluck('bai_viet_id')
                    ->toArray();

                $commentedPostIds = \DB::table('binh_luan')
                    ->where('nguoi_dung_id', $user->id)
                    ->pluck('bai_viet_id')
                    ->toArray();

                $interactedPostIds = array_unique(array_merge($likedPostIds, $bookmarkedPostIds, $commentedPostIds));

                if (!empty($interactedPostIds)) {
                    $preferredHashtags = \DB::table('bai_viet_hashtag')
                        ->whereIn('bai_viet_id', $interactedPostIds)
                        ->select('hashtag_id', \DB::raw('count(*) as count'))
                        ->groupBy('hashtag_id')
                        ->orderBy('count', 'desc')
                        ->take(5)
                        ->pluck('hashtag_id')
                        ->toArray();
                }
            }

            // Lấp đầy ngẫu nhiên các hashtag hot nhất nếu chưa có dữ liệu tương tác
            if (empty($preferredHashtags)) {
                $preferredHashtags = Hashtag::orderBy('so_bai_viet', 'desc')
                    ->take(5)
                    ->pluck('id')
                    ->toArray();
            }

            $query->whereHas('hashtags', function($q) use ($preferredHashtags) {
                $q->whereIn('hashtag_id', $preferredHashtags);
            });

            // Lấy tên các hashtag đề xuất để hiển thị ngoài UI
            $recommendedTagNames = Hashtag::whereIn('id', $preferredHashtags)->pluck('ten')->toArray();
        }

        // Apply time filter
        if ($time === 'today') {
            $query->where('created_at', '>=', now()->startOfDay());
        } elseif ($time === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        }

        // Apply sorting
        if ($isRecommendation) {
            // Sắp xếp theo Điểm tương tác: (reactions_count * 2) + (comments_count * 3) giảm dần (Thịnh hành)
            $query->orderByRaw('((reactions_count * 2) + (comments_count * 3)) DESC')
                  ->orderBy('created_at', 'desc');
        } else {
            if ($sort === 'popular') {
                $query->orderByRaw('(reactions_count + comments_count + shares_count) DESC')
                      ->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
        }

        // Paginate results
        $posts = $query->paginate(10)->withQueryString();

        // Get popular hashtags
        $popularHashtags = Hashtag::orderBy('so_bai_viet', 'desc')
            ->take(10)
            ->get()
            ->map(function ($tag) {
                // Find latest post with this hashtag that has media
                $latestPostWithMedia = $tag->posts()
                    ->where('da_xoa', false)
                    ->whereHas('media')
                    ->latest()
                    ->first();

                // If no media, find any latest post
                if (!$latestPostWithMedia) {
                    $latestPost = $tag->posts()
                        ->where('da_xoa', false)
                        ->latest()
                        ->first();
                } else {
                    $latestPost = $latestPostWithMedia;
                }

                $thumbnail = null;
                if ($latestPostWithMedia && $latestPostWithMedia->media->isNotEmpty()) {
                    $thumbnail = asset('storage/' . $latestPostWithMedia->media->first()->duong_dan);
                } elseif ($latestPost && $latestPost->user) {
                    $thumbnail = $latestPost->user->anh_dai_dien 
                        ? asset('storage/' . $latestPost->user->anh_dai_dien) 
                        : asset('storage/avatars/avtmacdinh.png');
                } else {
                    $thumbnail = asset('storage/avatars/avtmacdinh.png');
                }

                return [
                    'ten' => $tag->ten,
                    'so_bai_viet' => $tag->so_bai_viet,
                    'thumbnail' => $thumbnail,
                ];
            });

        // Get matched users if type is user
        $matchedUsers = collect();
        if ($keyword && $type === 'user') {
            $matchedUsers = \App\Models\User::where('con_hoat_dong', true)
                ->where(function($q) use ($keyword) {
                    $q->where('ten_dang_nhap', 'LIKE', "%{$keyword}%")
                      ->orWhere('email', 'LIKE', "%{$keyword}%");
                })
                ->limit(10)
                ->get();
        }

        return view('explore', [
            'posts' => $posts,
            'popularHashtags' => $popularHashtags,
            'matchedUsers' => $matchedUsers,
            'keyword' => $keyword,
            'type' => $type,
            'time' => $time,
            'sort' => $sort,
            'isRecommendation' => $isRecommendation,
            'recommendedTagNames' => $recommendedTagNames,
            'title' => __('messages.explore_title') ?? 'Khám phá',
            'message' => __('messages.explore_subtitle') ?? 'Tìm kiếm bài viết, hashtag hoặc người dùng',
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

        // Sync hashtags
        $this->syncHashtags($post);

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

        // Sync hashtags
        $this->syncHashtags($post);

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

    protected function syncHashtags(BaiViet $post)
    {
        $content = $post->noi_dung ?? '';
        preg_match_all('/(?<=^|(?<=[^a-zA-Z0-9_\.]))#([\p{L}\p{N}_]+)/u', $content, $matches);
        
        $tags = [];
        if (!empty($matches[1])) {
            $tags = array_unique(array_map('mb_strtolower', $matches[1]));
        }

        $tagIds = [];
        foreach ($tags as $tagName) {
            $hashtag = Hashtag::firstOrCreate(
                ['ten' => $tagName],
                ['so_bai_viet' => 0]
            );
            $tagIds[] = $hashtag->id;
        }

        $changes = $post->hashtags()->sync($tagIds);

        // Update counts for attached/detached/updated hashtags
        $allAffectedIds = array_unique(array_merge($changes['attached'], $changes['detached'], $changes['updated']));
        foreach ($allAffectedIds as $id) {
            $tag = Hashtag::find($id);
            if ($tag) {
                $count = $tag->posts()->count();
                if ($count === 0) {
                    $tag->delete();
                } else {
                    $tag->so_bai_viet = $count;
                    $tag->save();
                }
            }
        }
    }
}
