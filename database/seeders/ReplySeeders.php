<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BinhLuan;
use App\Models\BaiViet;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ReplySeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Đảm bảo có người dùng
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'ten_dang_nhap' => 'tester',
                'email' => 'tester@example.com',
                'mat_khau_hash' => Hash::make('password'),
                'con_hoat_dong' => true,
            ]);
        }

        // 2. Đảm bảo có bài viết
        $post = BaiViet::first();
        if (!$post) {
            $post = BaiViet::create([
                'nguoi_dung_id' => $user->id,
                'loai' => 'van_ban',
                'noi_dung' => 'Bài viết mẫu để test reply',
                'quyen_rieng_tu' => 'cong_khai',
            ]);
        }

        // 3. Tạo các bình luận gốc (parent comments)
        $parentComments = [
            'Bình luận gốc thứ nhất',
            'Bình luận gốc thứ hai',
            'Bình luận gốc thứ ba',
        ];

        foreach ($parentComments as $content) {
            $parent = BinhLuan::create([
                'bai_viet_id' => $post->id,
                'nguoi_dung_id' => $user->id,
                'binh_luan_cha_id' => null,
                'noi_dung' => $content,
            ]);

            // 4. Tạo các reply cho mỗi bình luận gốc
            for ($i = 1; $i <= 2; $i++) {
                BinhLuan::create([
                    'bai_viet_id' => $post->id,
                    'nguoi_dung_id' => $user->id,
                    'binh_luan_cha_id' => $parent->id,
                    'noi_dung' => "Reply $i cho: " . $content,
                ]);
            }
        }
    }
}
