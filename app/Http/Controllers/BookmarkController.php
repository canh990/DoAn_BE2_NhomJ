<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\BaiVietDaLuu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookmarkController extends Controller
{
    /**
     * Hiển thị danh sách bài viết đã lưu của người dùng hiện tại.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Lấy danh sách bài viết đã lưu của user, eager load đầy đủ để tối ưu hóa truy vấn
        $posts = BaiViet::query()
            ->join('bai_viet_da_luu', 'bai_viet.id', '=', 'bai_viet_da_luu.bai_viet_id')
            ->where('bai_viet_da_luu.nguoi_dung_id', $user->id)
            ->where('bai_viet.da_xoa', false)
            ->select('bai_viet.*')
            ->with(['user', 'media', 'originalPost.user', 'originalPost.media'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with([
                'reactions' => function ($query) use ($user) {
                    $query->where('nguoi_dung_id', $user->id);
                },
                'comments' => function ($query) {
                    $query->whereNull('binh_luan_cha_id')
                          ->with(['user', 'nestedChildren'])
                          ->latest('ngay_tao');
                },
                'bookmarks' => function ($query) use ($user) {
                    $query->where('nguoi_dung_id', $user->id);
                }
            ])
            ->orderBy('bai_viet_da_luu.ngay_tao', 'desc')
            ->paginate(15);

        // Hỗ trợ AJAX trả về JSON nếu cần thiết
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        }

        return view('posts.bookmarks', compact('posts'));
    }

    /**
     * Lưu hoặc bỏ lưu bài viết (Toggle Bookmark).
     */
    public function toggle(Request $request, BaiViet $post)
    {
        $user = $request->user();

        // Không cho phép lưu bài viết đã bị xóa
        if ($post->da_xoa) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể lưu bài viết đã bị xóa.'
                ], 404);
            }
            return back()->with('error', 'Bài viết không tồn tại hoặc đã bị xóa.');
        }

        $bookmark = BaiVietDaLuu::where('nguoi_dung_id', $user->id)
            ->where('bai_viet_id', $post->id)
            ->first();

        if ($bookmark) {
            // Đã lưu -> Tiến hành bỏ lưu
            $bookmark->delete();
            $isBookmarked = false;
            $message = 'Đã bỏ lưu bài viết.';
        } else {
            // Chưa lưu -> Tiến hành lưu (Dùng firstOrCreate tránh trùng lặp do race condition)
            BaiVietDaLuu::firstOrCreate([
                'nguoi_dung_id' => $user->id,
                'bai_viet_id' => $post->id
            ]);
            $isBookmarked = true;
            $message = 'Đã lưu bài viết thành công.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_bookmarked' => $isBookmarked,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }
}
