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
        // Tắt kiểm tra khóa ngoại để tránh lỗi khi truncate hoặc chèn dữ liệu
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Đảm bảo có người dùng
        $user = User::first();
        if (!$user) {
            $userId = \Illuminate\Support\Facades\DB::table('nguoi_dung')->insertGetId([
                'ten_dang_nhap' => 'tester',
                'email' => 'tester@example.com',
                'mat_khau_hash' => Hash::make('password'),
                'con_hoat_dong' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $userId = $user->id;
        }

        // 2. Đảm bảo có bài viết
        $post = BaiViet::first();
        if (!$post) {
            $postId = \Illuminate\Support\Facades\DB::table('bai_viet')->insertGetId([
                'nguoi_dung_id' => $userId,
                'loai' => 'van_ban',
                'noi_dung' => 'Bài viết mẫu để test reply',
                'quyen_rieng_tu' => 'cong_khai',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $postId = $post->id;
        }

        // 3. Tạo các bình luận gốc (parent comments)
        $parentComments = [
            'Bình luận gốc thứ nhất',
            'Bình luận gốc thứ hai',
            'Bình luận gốc thứ ba',
        ];

        foreach ($parentComments as $content) {
            $parentId = \Illuminate\Support\Facades\DB::table('binh_luan')->insertGetId([
                'bai_viet_id' => $postId,
                'nguoi_dung_id' => $userId,
                'binh_luan_cha_id' => null,
                'noi_dung' => $content,
                'da_xoa' => false,
                'ngay_tao' => now(),
                'ngay_cap_nhat' => now(),
            ]);

            // 4. Tạo các reply cho mỗi bình luận gốc
            for ($i = 1; $i <= 2; $i++) {
                \Illuminate\Support\Facades\DB::table('binh_luan')->insert([
                    'bai_viet_id' => $postId,
                    'nguoi_dung_id' => $userId,
                    'binh_luan_cha_id' => $parentId,
                    'noi_dung' => "Reply $i cho: " . $content,
                    'da_xoa' => false,
                    'ngay_tao' => now(),
                    'ngay_cap_nhat' => now(),
                ]);
            }
        }

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
