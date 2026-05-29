<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BaiVietDaLuuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bai_viet_da_luu')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = DB::table('nguoi_dung')->pluck('id')->toArray();
        $posts = DB::table('bai_viet')->pluck('id')->toArray();

        if (empty($users) || empty($posts)) {
            $this->command->error('Vui lòng chạy NguoiDungSeeder và BaiVietSeeder trước!');
            return;
        }

        $savedPosts = [];
        $insertedPairs = [];

        foreach ($users as $userId) {
            // Mỗi người dùng lưu ngẫu nhiên từ 1 đến 3 bài viết
            $numSaved = rand(1, min(3, count($posts)));
            $randomPosts = array_rand(array_flip($posts), $numSaved);
            $randomPosts = is_array($randomPosts) ? $randomPosts : [$randomPosts];

            foreach ($randomPosts as $postId) {
                $pairKey = "{$userId}-{$postId}";
                if (isset($insertedPairs[$pairKey])) {
                    continue;
                }

                $savedPosts[] = [
                    'nguoi_dung_id' => $userId,
                    'bai_viet_id' => $postId,
                    'ngay_tao' => Carbon::now()->subDays(rand(1, 15)),
                ];

                $insertedPairs[$pairKey] = true;
            }
        }

        if (!empty($savedPosts)) {
            DB::table('bai_viet_da_luu')->insert($savedPosts);
        }

        $this->command->info('✅ BaiVietDaLuuSeeder: Đã tạo ' . count($savedPosts) . ' bài viết đã lưu.');
    }
}
