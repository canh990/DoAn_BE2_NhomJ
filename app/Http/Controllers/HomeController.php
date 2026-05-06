<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $posts = BaiViet::with(['user', 'media'])
            ->where('da_xoa', false)
            ->latest()
            ->take(20)
            ->get();

        return view('components.home', [
            'posts' => $posts,
        ]);
    }
}
