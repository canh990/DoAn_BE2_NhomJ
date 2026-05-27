<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BaiVietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bai_viet')->truncate();
        DB::table('media_bai_viet')->truncate();
        DB::table('binh_chon')->truncate();
        DB::table('lua_chon_binh_chon')->truncate();
        DB::table('phieu_bau')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Đảm bảo đã có người dùng
        $nguoiDungIds = DB::table('nguoi_dung')->pluck('id')->toArray();
        if (empty($nguoiDungIds)) {
            $this->command->error('Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $now = Carbon::now();

        // 1. Dữ liệu bài viết mẫu
        $postTemplates = [
            [
                'loai' => 'van_ban',
                'noi_dung' => 'Chào cả nhà! Hôm nay là một ngày tuyệt vời để học lập trình Laravel.',
                'ten_dia_diem' => 'Hà Nội, Việt Nam',
                'vi_do' => 21.028511,
                'kinh_do' => 105.804817,
                'cam_xuc' => 'vui_ve',
                'hoat_dong' => 'đang học tập',
                'quyen_rieng_tu' => 'cong_khai',
            ],
            [
                'loai' => 'van_ban',
                'noi_dung' => 'Có ai biết quán cafe nào yên tĩnh làm việc ở Sài Gòn không? Cho mình xin vài gợi ý với.',
                'ten_dia_diem' => 'Hồ Chí Minh, Việt Nam',
                'vi_do' => 10.823099,
                'kinh_do' => 106.629664,
                'cam_xuc' => 'tim_kiem',
                'hoat_dong' => 'đang thư giãn',
                'quyen_rieng_tu' => 'cong_khai',
            ],
            [
                'loai' => 'hinh_anh',
                'noi_dung' => 'Chuyến du lịch Sa Pa tuyệt đẹp tuần trước! Cảnh sắc thiên nhiên thật hùng vĩ.',
                'ten_dia_diem' => 'Sa Pa, Lào Cai',
                'vi_do' => 22.3364,
                'kinh_do' => 103.8438,
                'cam_xuc' => 'hao_huc',
                'hoat_dong' => 'đang du lịch',
                'quyen_rieng_tu' => 'cong_khai',
                'media' => [
                    ['loai' => 'hinh_anh', 'duong_dan' => 'https://picsum.photos/800/600?random=11', 'thu_tu' => 0],
                    ['loai' => 'hinh_anh', 'duong_dan' => 'https://picsum.photos/800/600?random=12', 'thu_tu' => 1]
                ]
            ],
            [
                'loai' => 'hinh_anh',
                'noi_dung' => 'Hôm nay tự tay vào bếp nấu món phở bò truyền thống. Mời cả nhà cùng thưởng thức nha!',
                'ten_dia_diem' => 'Bếp Nhà',
                'vi_do' => null,
                'kinh_do' => null,
                'cam_xuc' => 'hanh_phuc',
                'hoat_dong' => 'đang nấu ăn',
                'quyen_rieng_tu' => 'ban_be',
                'media' => [
                    ['loai' => 'hinh_anh', 'duong_dan' => 'https://picsum.photos/800/600?random=13', 'thu_tu' => 0]
                ]
            ],
            [
                'loai' => 'video',
                'noi_dung' => 'Khoảnh khắc chú mèo con đáng yêu đùa nghịch với cuộn len. Siêu cute luôn ý!',
                'ten_dia_diem' => null,
                'vi_do' => null,
                'kinh_do' => null,
                'cam_xuc' => 'yeu_thuong',
                'hoat_dong' => 'đang giải trí',
                'quyen_rieng_tu' => 'cong_khai',
                'media' => [
                    ['loai' => 'video', 'duong_dan' => 'https://www.w3schools.com/html/mov_bbb.mp4', 'thu_tu' => 0]
                ]
            ],
            [
                'loai' => 'binh_chon',
                'noi_dung' => 'Các bạn thích học ngôn ngữ lập trình nào nhất cho phát triển Web?',
                'ten_dia_diem' => null,
                'vi_do' => null,
                'kinh_do' => null,
                'cam_xuc' => 'to_mo',
                'hoat_dong' => 'đang khảo sát',
                'quyen_rieng_tu' => 'cong_khai',
                'poll' => [
                    'cau_hoi' => 'Ngôn ngữ Web yêu thích của bạn?',
                    'options' => ['PHP (Laravel)', 'JavaScript (NodeJS)', 'Python (Django)', 'Go (Gin)']
                ]
            ],
            [
                'loai' => 'binh_chon',
                'noi_dung' => 'Kế hoạch đi chơi cuối tuần này thế nào đây mọi người ơi?',
                'ten_dia_diem' => 'Văn Phòng',
                'vi_do' => null,
                'kinh_do' => null,
                'cam_xuc' => 'phan_khich',
                'hoat_dong' => 'đang lên kế hoạch',
                'quyen_rieng_tu' => 'ban_be',
                'poll' => [
                    'cau_hoi' => 'Cuối tuần này đi đâu chơi?',
                    'options' => ['Đi xem phim', 'Đi cắm trại ngoại thành', 'Đi ăn lẩu', 'Ở nhà ngủ']
                ]
            ],
            [
                'loai' => 'van_ban',
                'noi_dung' => 'Đây là một bài viết ở chế độ riêng tư, chỉ những người được chấp nhận mới có thể xem.',
                'ten_dia_diem' => null,
                'vi_do' => null,
                'kinh_do' => null,
                'cam_xuc' => 'bi_mat',
                'hoat_dong' => 'đang suy ngẫm',
                'quyen_rieng_tu' => 'rieng_tu',
            ]
        ];

        // Thêm các bài viết bổ sung cho đủ số lượng và đa dạng tác giả
        foreach ($postTemplates as $idx => $tpl) {
            $tacGiaId = $nguoiDungIds[array_rand($nguoiDungIds)];
            
            // Tạo bài viết
            $postId = DB::table('bai_viet')->insertGetId([
                'nguoi_dung_id' => $tacGiaId,
                'bai_goc_id' => null,
                'loai' => $tpl['loai'],
                'noi_dung' => $tpl['noi_dung'],
                'ten_dia_diem' => $tpl['ten_dia_diem'],
                'vi_do' => $tpl['vi_do'],
                'kinh_do' => $tpl['kinh_do'],
                'cam_xuc' => $tpl['cam_xuc'],
                'hoat_dong' => $tpl['hoat_dong'],
                'quyen_rieng_tu' => $tpl['quyen_rieng_tu'],
                'da_ghim' => ($idx === 0), // ghim bài đầu tiên
                'da_chinh_sua' => false,
                'da_xoa' => false,
                'created_at' => $now->copy()->subHours(count($postTemplates) - $idx),
                'updated_at' => $now->copy()->subHours(count($postTemplates) - $idx),
            ]);

            // Thêm media nếu có
            if (isset($tpl['media'])) {
                foreach ($tpl['media'] as $med) {
                    DB::table('media_bai_viet')->insert([
                        'bai_viet_id' => $postId,
                        'loai' => $med['loai'],
                        'duong_dan' => $med['duong_dan'],
                        'thu_tu' => $med['thu_tu'],
                        'ngay_tao' => $now,
                    ]);
                }
            }

            // Thêm bình chọn nếu có
            if (isset($tpl['poll'])) {
                $pollId = DB::table('binh_chon')->insertGetId([
                    'bai_viet_id' => $postId,
                    'cau_hoi' => $tpl['poll']['cau_hoi'],
                    'ngay_ket_thuc' => $now->copy()->addDays(7),
                    'ngay_tao' => $now,
                ]);

                $optIds = [];
                foreach ($tpl['poll']['options'] as $optText) {
                    $optIds[] = DB::table('lua_chon_binh_chon')->insertGetId([
                        'binh_chon_id' => $pollId,
                        'noi_dung' => $optText,
                        'ngay_tao' => $now,
                    ]);
                }

                // Giả lập một vài lượt bình chọn từ người dùng
                $voters = $nguoiDungIds;
                shuffle($voters);
                $numVotes = rand(2, count($voters)); // Chọn ngẫu nhiên số lượng người vote
                
                for ($v = 0; $v < $numVotes; $v++) {
                    $voterId = $voters[$v];
                    $randomOptId = $optIds[array_rand($optIds)];
                    
                    DB::table('phieu_bau')->insert([
                        'binh_chon_id' => $pollId,
                        'lua_chon_id' => $randomOptId,
                        'nguoi_dung_id' => $voterId,
                        'ngay_tao' => $now,
                    ]);
                }
            }
        }

        // Tạo thêm 60 bài viết văn bản, hình ảnh, hoặc khảo sát ngẫu nhiên
        $postTypes = ['van_ban', 'hinh_anh', 'binh_chon'];
        $locations = ['Ha Noi', 'Sai Gon', 'Da Nang', 'Nha Trang', 'Da Lat', null];
        $emotions = ['vui_ve', 'hao_huc', 'hanh_phuc', 'phan_khich', 'to_mo', 'yeu_thuong', null];
        $activities = ['dang hoc tap', 'dang du lich', 'dang nau an', 'dang giai tri', 'dang len ke hoach', null];

        for ($i = 1; $i <= 60; $i++) {
            $tacGiaId = $nguoiDungIds[array_rand($nguoiDungIds)];
            $loai = $postTypes[array_rand($postTypes)];
            $location = $locations[array_rand($locations)];
            $emotion = $emotions[array_rand($emotions)];
            $activity = $activities[array_rand($activities)];
            
            $postId = DB::table('bai_viet')->insertGetId([
                'nguoi_dung_id' => $tacGiaId,
                'loai' => $loai,
                'noi_dung' => "Chia se cuoc song day mau sac so {$i} tu thanh vien gia dinh mang xa hoi.",
                'ten_dia_diem' => $location,
                'vi_do' => $location ? rand(10, 22) + (rand(1, 999999) / 1000000) : null,
                'kinh_do' => $location ? rand(103, 108) + (rand(1, 999999) / 1000000) : null,
                'cam_xuc' => $emotion,
                'hoat_dong' => $activity,
                'quyen_rieng_tu' => rand(0, 4) === 0 ? 'ban_be' : 'cong_khai',
                'created_at' => $now->copy()->subHours(rand(1, 120)),
                'updated_at' => $now,
            ]);

            if ($loai === 'hinh_anh') {
                DB::table('media_bai_viet')->insert([
                    'bai_viet_id' => $postId,
                    'loai' => 'hinh_anh',
                    'duong_dan' => "https://picsum.photos/800/600?random=" . (100 + $i),
                    'thu_tu' => 0,
                    'ngay_tao' => $now,
                ]);
            } elseif ($loai === 'binh_chon') {
                $pollId = DB::table('binh_chon')->insertGetId([
                    'bai_viet_id' => $postId,
                    'cau_hoi' => "Khao sat y kien so {$i} ve chu de cuoc song?",
                    'ngay_ket_thuc' => $now->copy()->addDays(7),
                    'ngay_tao' => $now,
                ]);

                $opts = ["Lua chon A", "Lua chon B", "Lua chon C"];
                foreach ($opts as $optText) {
                    DB::table('lua_chon_binh_chon')->insert([
                        'binh_chon_id' => $pollId,
                        'noi_dung' => $optText,
                        'ngay_tao' => $now,
                    ]);
                }
            }
        }

        $this->command->info('✅ BaiVietSeeder: Đã tạo bài viết, media, bình chọn và phiếu bầu thành công!');
    }
}
