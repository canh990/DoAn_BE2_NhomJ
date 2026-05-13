<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\Tin24h;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $posts = BaiViet::with(['user', 'media', 'originalPost.user', 'originalPost.media'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }])
            ->where('da_xoa', false)
            ->whereIn('loai', ['van_ban', 'hinh_anh', 'chia_se'])
            ->latest()
            ->take(20)
            ->get();

        // Lấy stories chưa hết hạn, eager-load user
        $stories = Tin24h::with('user')
            ->conHan()  // scope trong model: het_han > now()
            ->latest('ngay_tao')
            ->take(30)
            ->get();

        // Lấy 9 ảnh/video mới nhất từ bảng tin
        $recentMedia = \App\Models\MediaBaiViet::whereIn('bai_viet_id', BaiViet::where('da_xoa', false)->pluck('id'))
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
