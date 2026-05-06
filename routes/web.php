<?php
require __DIR__ . '/Auth.php';
require __DIR__ . '/profile.php';
use Illuminate\Support\Facades\Route;   

Route::get('/', function () {
    return view('welcome');
});

Route::get('/post-card', function () {
    $user = (object) [
        'name' => 'NHOMJ Designer',
        'ten_dang_nhap' => 'nhomj.studio',
        'anh_dai_dien' => null,
        'da_xac_thuc' => true,
    ];

    $post = (object) [
        'noi_dung' => 'Đây là trang preview cho component post card. Bạn có thể dùng route này để kiểm tra giao diện nhanh trong lúc chỉnh sửa Blade.',
        'image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
        'created_at' => now()->subHours(2),
        'comments_count' => 18,
        'reactions_count' => 124,
        'shares_count' => 9,
        'views_count' => 560,
        'user' => $user,
    ];

    return view('post-card-preview', compact('post', 'user'));
})->name('post-card.preview');
