<?php

namespace Database\Seeders;

use App\Models\BaiViet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PostSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->take(3)->get();

        if ($users->isEmpty()) {
            $users = collect([
                User::create([
                    'ten_dang_nhap' => 'user_1',
                    'email' => 'user1@example.com',
                    'so_dien_thoai' => '0901000001',
                    'mat_khau_hash' => Hash::make('password123'),
                    'quyen_rieng_tu' => 'cong_khai',
                ]),
                User::create([
                    'ten_dang_nhap' => 'user_2',
                    'email' => 'user2@example.com',
                    'so_dien_thoai' => '0901000002',
                    'mat_khau_hash' => Hash::make('password123'),
                    'quyen_rieng_tu' => 'cong_khai',
                ]),
                User::create([
                    'ten_dang_nhap' => 'user_3',
                    'email' => 'user3@example.com',
                    'so_dien_thoai' => '0901000003',
                    'mat_khau_hash' => Hash::make('password123'),
                    'quyen_rieng_tu' => 'ban_be',
                ]),
            ]);
        }

        if (BaiViet::count() > 0) {
            return;
        }

        $posts = [
            [
                'nguoi_dung_id' => $users->get(0)->id,
                'loai' => 'van_ban',
                'noi_dung' => 'Hôm nay mình đi dạo công viên và thấy rất nhiều cây xanh.',
                'quyen_rieng_tu' => 'cong_khai',
            ],
            [
                'nguoi_dung_id' => $users->get(1)->id,
                'loai' => 'van_ban',
                'noi_dung' => 'Chia sẻ kinh nghiệm học Laravel cho người mới bắt đầu.',
                'quyen_rieng_tu' => 'ban_be',
            ],
            [
                'nguoi_dung_id' => $users->get(2)->id,
                'loai' => 'hinh_anh',
                'noi_dung' => 'Album ảnh du lịch Đà Nẵng cực chill.',
                'quyen_rieng_tu' => 'chi_minh',
            ],
            [
                'nguoi_dung_id' => $users->get(0)->id,
                'loai' => 'video',
                'noi_dung' => 'Video giới thiệu quán cà phê mới ở thành phố.',
                'quyen_rieng_tu' => 'cong_khai',
            ],
            [
                'nguoi_dung_id' => $users->get(1)->id,
                'loai' => 'van_ban',
                'noi_dung' => 'Lời chúc cuối tuần gửi đến mọi người.',
                'quyen_rieng_tu' => 'ban_be',
            ],
        ];

        foreach ($posts as $postData) {
            BaiViet::create($postData);
        }
    }
}
