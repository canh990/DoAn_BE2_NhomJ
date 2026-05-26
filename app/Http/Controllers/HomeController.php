<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\Tin24h;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $blockedUserIds = [];
        if (auth()->check()) {
            $user = auth()->user();
            $blockedByMe = $user->blockedUsers()->pluck('nguoi_bi_chan_id')->toArray();
            $blockedMe = $user->blockedByUsers()->pluck('nguoi_chan_id')->toArray();
            $blockedUserIds = array_unique(array_merge($blockedByMe, $blockedMe));
        }

        $posts = BaiViet::with(['user', 'media', 'originalPost.user', 'originalPost.media'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }, 'bookmarks' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }])
            ->where('da_xoa', false)
            ->whereIn('loai', ['van_ban', 'hinh_anh', 'chia_se'])
            ->when(!empty($blockedUserIds), function ($query) use ($blockedUserIds) {
                $query->whereNotIn('nguoi_dung_id', $blockedUserIds);
            })
            ->latest()
            ->take(20)
            ->get();

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
        ]);
    }
}
