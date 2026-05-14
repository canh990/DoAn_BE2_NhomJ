<?php

namespace Database\Seeders;

use App\Models\BinhLuan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaBinhLuanSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Lấy danh sách bình luận hiện có
        $comments = BinhLuan::all();

        if ($comments->isEmpty()) {
            $this->command->error('Vui lòng chạy BinhLuanSeeder hoặc ReplySeeders trước!');
            return;
        }

        // 2. Một số dữ liệu mẫu cho media
        $mediaSamples = [
            ['loai' => 'hinh_anh', 'duong_dan' => 'https://picsum.photos/400/300?random=1'],
            ['loai' => 'hinh_anh', 'duong_dan' => 'https://picsum.photos/400/300?random=2'],
            ['loai' => 'sticker', 'duong_dan' => 'https://example.com/stickers/happy.png'],
            ['loai' => 'gif', 'duong_dan' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNHJndXp4YXJ1eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/3o7TKMGpxx66d7V94A/giphy.gif'],
        ];

        // 3. Chọn ngẫu nhiên khoảng 30% số bình luận để thêm media
        $commentsToHaveMedia = $comments->random(min($comments->count(), max(1, (int)($comments->count() * 0.3))));

        foreach ($commentsToHaveMedia as $comment) {
            $sample = $mediaSamples[array_rand($mediaSamples)];
            
            DB::table('media_binh_luan')->insert([
                'binh_luan_id' => $comment->id,
                'loai' => $sample['loai'],
                'duong_dan' => $sample['duong_dan'],
                'ngay_tao' => now(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Đã tạo dữ liệu media cho bình luận thành công!');
    }
}
