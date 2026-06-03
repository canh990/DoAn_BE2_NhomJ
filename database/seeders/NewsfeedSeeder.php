<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewsfeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này tạo thêm các bài viết chất lượng cao cho Bảng tin (Newsfeed)
     * giúp giao diện bảng tin phong phú và sinh động hơn.
     */
    public function run(): void
    {
        $this->command->info('📰 Bắt đầu khởi tạo dữ liệu bài viết cho Bảng tin (Newsfeed)...');

        $users = DB::table('nguoi_dung')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ Không tìm thấy người dùng nào. Hãy chạy NguoiDungSeeder trước!');
            return;
        }

        $now = Carbon::now();
        $newsfeedTemplates = [
            [
                'noi_dung' => 'Chào buổi sáng mọi người! Chúc cả nhà một ngày làm việc đầy năng lượng và hiệu quả. #GoodMorning #Energy',
                'loai' => 'van_ban',
                'ten_dia_diem' => 'TP. Hồ Chí Minh',
                'cam_xuc' => 'phan_khich',
                'hoat_dong' => 'đang đón ngày mới',
            ],
            [
                'noi_dung' => 'Cuối tuần bình yên với một tách trà nóng và một cuốn sách hay. Bình yên đôi khi chỉ đơn giản thế này thôi. #Weekend #Peaceful',
                'loai' => 'hinh_anh',
                'ten_dia_diem' => 'Đà Lạt, Lâm Đồng',
                'cam_xuc' => 'vui_ve',
                'hoat_dong' => 'đang thư giãn',
                'media_url' => 'https://picsum.photos/800/600?random=201'
            ],
            [
                'noi_dung' => 'Vừa hoàn thành xong đồ án môn học Web 2! Cảm thấy nhẹ nhõm và tự hào về bản thân. Cảm ơn cả nhóm J đã cùng nỗ lực. #NhomJ #Laravel',
                'loai' => 'van_ban',
                'ten_dia_diem' => 'Đại học Công nghệ',
                'cam_xuc' => 'hanh_phuc',
                'hoat_dong' => 'đang ăn mừng',
            ],
            [
                'noi_dung' => 'Bữa tối tự tay chuẩn bị: bít tết bò Mỹ sốt tiêu đen. Ai muốn qua thưởng thức cùng không nào? 🥩🍷 #Cooking #DinnerTime',
                'loai' => 'hinh_anh',
                'ten_dia_diem' => 'Home Sweet Home',
                'cam_xuc' => 'hao_huc',
                'hoat_dong' => 'đang nấu ăn',
                'media_url' => 'https://picsum.photos/800/600?random=202'
            ]
        ];

        $insertedCount = 0;
        foreach ($newsfeedTemplates as $idx => $tpl) {
            $user = $users->random();
            
            $postId = DB::table('bai_viet')->insertGetId([
                'nguoi_dung_id' => $user->id,
                'bai_goc_id' => null,
                'loai' => $tpl['loai'],
                'noi_dung' => $tpl['noi_dung'],
                'ten_dia_diem' => $tpl['ten_dia_diem'],
                'vi_do' => null,
                'kinh_do' => null,
                'cam_xuc' => $tpl['cam_xuc'],
                'hoat_dong' => $tpl['hoat_dong'],
                'quyen_rieng_tu' => 'cong_khai',
                'da_ghim' => false,
                'da_chinh_sua' => false,
                'da_xoa' => false,
                'created_at' => $now->copy()->subMinutes(rand(5, 300)),
                'updated_at' => $now,
            ]);

            $insertedCount++;

            if ($tpl['loai'] === 'hinh_anh' && isset($tpl['media_url'])) {
                DB::table('media_bai_viet')->insert([
                    'bai_viet_id' => $postId,
                    'loai' => 'hinh_anh',
                    'duong_dan' => $tpl['media_url'],
                    'thu_tu' => 0,
                    'ngay_tao' => $now,
                ]);
            }
        }

        $this->command->info("✅ Hoàn tất! Đã tạo thêm {$insertedCount} bài viết chất lượng cao cho Bảng tin.");
    }
}
