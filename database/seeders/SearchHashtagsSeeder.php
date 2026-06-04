<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchHashtagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này khởi tạo dữ liệu bài viết chứa các Hashtag cụ thể phục vụ cho 
     * tính năng Tìm kiếm Hashtag (Search Hashtags) và hiển thị bài viết gắn thẻ.
     */
    public function run(): void
    {
        $this->command->info('🔍 Bắt đầu khởi tạo dữ liệu phục vụ Tìm kiếm Hashtags...');

        $users = DB::table('nguoi_dung')->get();

        if ($users->isEmpty()) {
            $this->command->error('Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $now = Carbon::now();

        // Danh sách các hashtag và bài viết mẫu tương ứng để kiểm thử tìm kiếm
        $hashtagPosts = [
            'laravel' => [
                'Học lập trình #laravel từ cơ bản đến nâng cao cực kỳ đơn giản và hiệu quả.',
                'Tính năng mới trong #laravel 11 giúp rút ngắn thời gian phát triển ứng dụng.',
                'Chia sẻ cấu trúc thư mục chuẩn cho dự án sử dụng #laravel.'
            ],
            'php' => [
                'Ngôn ngữ lập trình #php vẫn đang thống trị thế giới web với hơn 75% thị phần.',
                'Cập nhật những tính năng mới nhất của #php 8.3 mà bạn nên biết.'
            ],
            'nhomj' => [
                'Sản phẩm mạng xã hội thu nhỏ của #nhomj đang bước vào giai đoạn hoàn thiện cuối cùng.',
                'Cảm ơn các thành viên #nhomj đã nỗ lực hết mình trong suốt dự án này!'
            ],
            'search' => [
                'Xây dựng công cụ #search tối ưu hóa tốc độ tìm kiếm cho người dùng.',
                'Mẹo thiết kế thanh #search tinh tế, hiện đại cho ứng dụng Web.'
            ],
            'hashtags' => [
                'Tính năng tự động phân tích và trích xuất #hashtags từ nội dung bài viết.',
                'Cách sử dụng #hashtags hiệu quả để tăng lượt tiếp cận cho bài đăng.'
            ],
            'coding' => [
                'Trải nghiệm một buổi tối cực chill vừa nghe lofi vừa #coding dự án thú vị.',
                'Thói quen tốt khi #coding giúp giảm thiểu tối đa các lỗi vặt (bugs).'
            ],
            'webdev' => [
                'Xu hướng công nghệ #webdev nổi bật nhất trong năm 2026.',
                'Lộ trình trở thành một nhà phát triển #webdev chuyên nghiệp.'
            ]
        ];

        $insertedCount = 0;

        foreach ($hashtagPosts as $tag => $contents) {
            foreach ($contents as $content) {
                // Kiểm tra xem bài viết với nội dung này đã tồn tại chưa để tránh trùng lặp
                $exists = DB::table('bai_viet')
                    ->where('noi_dung', $content)
                    ->exists();

                if (!$exists) {
                    $randomUser = $users->random();
                    
                    $postId = DB::table('bai_viet')->insertGetId([
                        'nguoi_dung_id' => $randomUser->id,
                        'bai_goc_id' => null,
                        'loai' => 'van_ban',
                        'noi_dung' => $content,
                        'quyen_rieng_tu' => 'cong_khai',
                        'da_xoa' => false,
                        'created_at' => $now->copy()->subHours(rand(1, 48)),
                        'updated_at' => $now,
                    ]);

                    $insertedCount++;
                }
            }
        }

        $this->command->info("📝 Đã tạo thành công {$insertedCount} bài viết mới chứa Hashtag tìm kiếm.");

        // Quét và cập nhật bảng hashtag & bai_viet_hashtag
        $this->command->info('🏷️ Đang đồng bộ hóa Hashtags và cập nhật số lượng bài viết...');

        $allPosts = DB::table('bai_viet')
            ->where('da_xoa', false)
            ->whereNotNull('noi_dung')
            ->get();

        foreach ($allPosts as $post) {
            $content = $post->noi_dung;
            preg_match_all('/(?<=^|(?<=[^a-zA-Z0-9_\.]))#([\p{L}\p{N}_]+)/u', $content, $matches);
            
            if (!empty($matches[1])) {
                $tags = array_unique(array_map('mb_strtolower', $matches[1]));
                foreach ($tags as $tagName) {
                    // Tạo hashtag nếu chưa có
                    DB::table('hashtag')->insertOrIgnore([
                        'ten' => $tagName,
                        'so_bai_viet' => 0,
                        'ngay_tao' => $now,
                    ]);

                    $tag = DB::table('hashtag')->where('ten', $tagName)->first();

                    if ($tag) {
                        // Tạo liên kết bài viết - hashtag
                        DB::table('bai_viet_hashtag')->insertOrIgnore([
                            'bai_viet_id' => $post->id,
                            'hashtag_id' => $tag->id,
                        ]);
                    }
                }
            }
        }

        // Cập nhật lại cột so_bai_viet cho tất cả hashtag
        $hashtags = DB::table('hashtag')->get();
        foreach ($hashtags as $hashtag) {
            $count = DB::table('bai_viet_hashtag')->where('hashtag_id', $hashtag->id)->count();
            if ($count === 0) {
                DB::table('hashtag')->where('id', $hashtag->id)->delete();
            } else {
                DB::table('hashtag')->where('id', $hashtag->id)->update([
                    'so_bai_viet' => $count,
                ]);
            }
        }

        $this->command->info('✅ Hoàn thành khởi tạo dữ liệu Tìm kiếm Hashtags!');
    }
}
