<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\Tin24h;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $posts = BaiViet::with(['user', 'media'])
            ->withCount(['reactions', 'comments'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->with('user')->latest('ngay_tao')->limit(3);
            }])
            ->where('da_xoa', false)
            ->whereIn('loai', ['van_ban', 'hinh_anh'])
            ->latest()
            ->take(20)
            ->get();

        // Lấy stories chưa hết hạn, eager-load user
        $stories = Tin24h::with('user')
            ->conHan()  // scope trong model: het_han > now()
            ->latest('ngay_tao')
            ->take(30)
            ->get();

        return view('components.home', [
            'posts'   => $posts,
            'stories' => $stories,
        ]);
    }
}
