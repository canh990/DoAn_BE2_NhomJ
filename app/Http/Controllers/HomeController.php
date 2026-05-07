<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
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

        return view('components.home', [
            'posts' => $posts,
        ]);
    }
}
