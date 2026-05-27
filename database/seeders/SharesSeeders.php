<?php

namespace Database\Seeders;

use App\Models\BaiViet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SharesSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Đảm bảo có người dùng
        $users = User::all();
        if ($users->isEmpty()) {
            // Nếu không có user nào, thoát hoặc báo lỗi (thường nên chạy NguoiDungSeeder trước)
            $this->command->error('Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        // 2. Lấy các bài viết gốc (không phải là bản chia sẻ)
        $originalPosts = BaiViet::whereNull('bai_goc_id')->get();
        if ($originalPosts->isEmpty()) {
            $this->command->error('Vui lòng chạy PostSeeders trước!');
            return;
        }

        // 3. Tạo dữ liệu chia sẻ
        foreach ($originalPosts as $index => $post) {
            // Mỗi bài viết gốc sẽ được chia sẻ bởi 1-2 người dùng ngẫu nhiên
            $shapers = $users->random(min(2, $users->count()));

            foreach ($shapers as $user) {
                // Tránh trường hợp người dùng chia sẻ lại bài của chính mình (tùy logic app)
                // if ($user->id === $post->nguoi_dung_id) continue;

                DB::table('bai_viet')->insert([
                    'nguoi_dung_id' => $user->id,
                    'bai_goc_id' => $post->id,
                    'loai' => 'chia_se',
                    'noi_dung' => 'Mình chia sẻ lại bài viết này, hay quá!',
                    'quyen_rieng_tu' => 'cong_khai',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 4. Đảm bảo thanh_truc chia sẻ bài viết để kiểm tra tích xanh và tính năng chia sẻ
        $thanhTruc = User::where('ten_dang_nhap', 'thanh_truc')->first();
        if ($thanhTruc && !$originalPosts->isEmpty()) {
            $firstOriginalPost = $originalPosts->first();
            DB::table('bai_viet')->insert([
                'nguoi_dung_id' => $thanhTruc->id,
                'bai_goc_id' => $firstOriginalPost->id,
                'loai' => 'chia_se',
                'noi_dung' => 'Bai viet nay rat hay va y nghia, moi nguoi cung doc nhe!',
                'quyen_rieng_tu' => 'cong_khai',
                'created_at' => now()->subMinutes(10),
                'updated_at' => now()->subMinutes(10),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Đã tạo dữ liệu chia sẻ thành công!');
    }
}
