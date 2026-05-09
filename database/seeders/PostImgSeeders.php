<?php

namespace Database\Seeders;

use App\Models\BaiViet;
use App\Models\MediaBaiViet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PostImgSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (BaiViet::where('loai', 'hinh_anh')->exists()) {
            return;
        }

        $users = User::query()->limit(3)->get();

        // Tạo user nếu chưa đủ 3
        if ($users->count() < 3) {
            if (!User::where('ten_dang_nhap', 'user_1')->exists()) {
                User::create([
                    'ten_dang_nhap' => 'user_1',
                    'email' => 'user1@example.com',
                    'so_dien_thoai' => '0901000001',
                    'mat_khau_hash' => Hash::make('password123'),
                    'quyen_rieng_tu' => 'cong_khai',
                    'da_xac_thuc' => true,
                ]);
            }
            if (!User::where('ten_dang_nhap', 'user_2')->exists()) {
                User::create([
                    'ten_dang_nhap' => 'user_2',
                    'email' => 'user2@example.com',
                    'so_dien_thoai' => '0901000002',
                    'mat_khau_hash' => Hash::make('password123'),
                    'quyen_rieng_tu' => 'cong_khai',
                    'da_xac_thuc' => true,
                ]);
            }
            if (!User::where('ten_dang_nhap', 'user_3')->exists()) {
                User::create([
                    'ten_dang_nhap' => 'user_3',
                    'email' => 'user3@example.com',
                    'so_dien_thoai' => '0901000003',
                    'mat_khau_hash' => Hash::make('password123'),
                    'quyen_rieng_tu' => 'chi_minh',
                    'da_xac_thuc' => true,
                ]);
            }

            $users = User::whereIn('ten_dang_nhap', ['user_1', 'user_2', 'user_3'])->get();
        }

        $posts = [
            [
                'nguoi_dung_id' => $users[0]->id,
                'loai' => 'hinh_anh',
                'noi_dung' => 'Một bộ ảnh hoàng hôn tuyệt đẹp phía sau cầu sông.',
                'quyen_rieng_tu' => 'cong_khai',
                'media' => [
                    'uploads/posts/sunset-1.jpg',
                    'uploads/posts/sunset-2.jpg',
                    'uploads/posts/sunset-3.jpg',
                ],
            ],
            [
                'nguoi_dung_id' => $users[1]->id,
                'loai' => 'hinh_anh',
                'noi_dung' => 'Những khoảnh khắc cuối tuần cùng bạn bè.',
                'quyen_rieng_tu' => 'ban_be',
                'media' => [
                    'uploads/posts/weekend-1.jpg',
                    'uploads/posts/weekend-2.jpg',
                ],
            ],
            [
                'nguoi_dung_id' => $users[2]->id,
                'loai' => 'hinh_anh',
                'noi_dung' => 'Ảnh ẩm thực ngon miệng cho buổi tối cuối tuần.',
                'quyen_rieng_tu' => 'cong_khai',
                'media' => [
                    'storage/app/public/posts/4h2PzIRIL3aYIjidC8hiaAJHjbH00YDdcIrygA7d.jpg',
                ],
            ],
        ];

        foreach ($posts as $postData) {
            $mediaList = $postData['media'];
            unset($postData['media']);

            $post = BaiViet::create($postData);

            foreach ($mediaList as $index => $path) {
                MediaBaiViet::create([
                    'bai_viet_id' => $post->id,
                    'loai' => 'hinh_anh',
                    'duong_dan' => $path,
                    'thu_tu' => $index,
                ]);
            }
        }
    }
}