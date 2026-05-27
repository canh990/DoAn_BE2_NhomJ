<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Tin24hSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tin_24h')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = DB::table('nguoi_dung')->pluck('id')->toArray();

        if (empty($users)) {
            $this->command->error('Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $now = Carbon::now();
        $stories = [];

        // Một số ảnh story mẫu
        $storyMedias = [
            ['loai' => 'hinh_anh', 'url' => 'https://picsum.photos/1080/1920?random=21'],
            ['loai' => 'hinh_anh', 'url' => 'https://picsum.photos/1080/1920?random=22'],
            ['loai' => 'hinh_anh', 'url' => 'https://picsum.photos/1080/1920?random=23'],
            ['loai' => 'hinh_anh', 'url' => 'https://picsum.photos/1080/1920?random=24'],
            ['loai' => 'hinh_anh', 'url' => 'https://picsum.photos/1080/1920?random=25'],
        ];

        foreach ($users as $userId) {
            // Mỗi user có cơ hội đăng từ 1 đến 3 story
            $numStories = rand(1, 3);
            for ($s = 0; $s < $numStories; $s++) {
                $media = $storyMedias[array_rand($storyMedias)];
                $ngayTao = $now->copy()->subHours(rand(1, 20)); // Đăng trong vòng 20h qua
                $hetHan = $ngayTao->copy()->addHours(24);       // Hết hạn sau 24h từ lúc đăng
                
                $stories[] = [
                    'nguoi_dung_id' => $userId,
                    'duong_dan_media' => $media['url'],
                    'loai_media' => $media['loai'],
                    'quyen_rieng_tu' => rand(0, 3) === 0 ? 'ban_be' : 'cong_khai',
                    'ngay_tao' => $ngayTao,
                    'het_han' => $hetHan,
                ];
            }
        }

        if (!empty($stories)) {
            DB::table('tin_24h')->insert($stories);
        }

        $this->command->info('✅ Tin24hSeeder: Đã tạo ' . count($stories) . ' tin 24h cho người dùng.');
    }
}
