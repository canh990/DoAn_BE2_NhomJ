<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\Tin24h;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $feedType = $request->input('feed', 'recommend');
        if (!in_array($feedType, ['recommend', 'following'])) {
            $feedType = 'recommend';
        }

        $blockedUserIds = [];
        $followingIds = [];
        $user = auth()->user();

        if ($user) {
            $blockedByMe = $user->blockedUsers()->pluck('nguoi_bi_chan_id')->toArray();
            $blockedMe = $user->blockedByUsers()->pluck('nguoi_chan_id')->toArray();
            $blockedUserIds = array_unique(array_merge($blockedByMe, $blockedMe));

            $followingIds = \DB::table('theo_doi')
                ->where('nguoi_theo_doi_id', $user->id)
                ->where('trang_thai', 'da_chap_nhan')
                ->pluck('nguoi_duoc_theo_doi_id')
                ->toArray();
        }

        $query = BaiViet::with(['user', 'media', 'originalPost.user', 'originalPost.media', 'poll.options.votes', 'poll.votes'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }, 'bookmarks' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }])
            ->where('da_xoa', false)
            ->whereIn('loai', ['van_ban', 'hinh_anh', 'chia_se', 'binh_chon']);

        if (!empty($blockedUserIds)) {
            $query->whereNotIn('nguoi_dung_id', $blockedUserIds);
        }

        if ($feedType === 'following' && $user) {
            // Chronological feed: CHỈ lấy bài viết của những người mà User đang đăng nhập theo dõi
            if (!empty($followingIds)) {
                $query->whereIn('nguoi_dung_id', $followingIds);
            } else {
                // Nếu chưa theo dõi ai, trả về danh sách rỗng
                $query->whereRaw('1 = 0');
            }
            $query->latest();
        } else {
            // Tab Dành cho bạn / Mặc định: Hiển thị tất cả bài viết trên toàn hệ thống, mới nhất lên đầu
            $query->latest();
        }

        $posts = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.posts-feed', [
                'posts'    => $posts,
                'feedType' => $feedType,
            ])->render();
        }

        // Lấy stories chưa hết hạn, eager-load user
        $stories = Tin24h::with('user')
            ->conHan()  // scope trong model: het_han > now()
            ->when(!empty($blockedUserIds), function ($query) use ($blockedUserIds) {
                $query->whereNotIn('nguoi_dung_id', $blockedUserIds);
            })
            ->latest('ngay_tao')
            ->take(30)
            ->get();

        // Lấy 9 ảnh/video mới nhất từ bảng tin
        $recentMedia = \App\Models\MediaBaiViet::whereIn('bai_viet_id', BaiViet::where('da_xoa', false)
            ->when(!empty($blockedUserIds), function ($query) use ($blockedUserIds) {
                $query->whereNotIn('nguoi_dung_id', $blockedUserIds);
            })->pluck('id'))
            ->latest('ngay_tao')
            ->take(9)
            ->get();

        return view('components.home', [
            'posts'       => $posts,
            'stories'     => $stories,
            'recentMedia' => $recentMedia,
            'feedType'    => $feedType,
        ]);
    }
}
