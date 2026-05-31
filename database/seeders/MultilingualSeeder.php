<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MultilingualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này dùng để khởi tạo hoặc cập nhật cấu hình Ngôn ngữ đa ngôn ngữ (Multilingual)
     * cho toàn bộ người dùng trong hệ thống (gồm Tiếng Việt - 'vi' và Tiếng Anh - 'en').
     */
    public function run(): void
    {
        $this->command->info('🌐 Bắt đầu khởi tạo dữ liệu cấu hình Đa ngôn ngữ (Multilingual) cho người dùng...');

        // Lấy danh sách toàn bộ người dùng trong hệ thống
        $users = DB::table('nguoi_dung')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ Không tìm thấy người dùng nào trong cơ sở dữ liệu. Hãy chạy NguoiDungSeeder trước!');
            return;
        }

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($users as $user) {
            // Kiểm tra cấu hình cài đặt hiện tại của người dùng
            $exists = DB::table('cai_dat_nguoi_dung')
                ->where('nguoi_dung_id', $user->id)
                ->exists();

            // Phân bổ ngẫu nhiên tỉ lệ 50-50 giữa Tiếng Việt ('vi') và Tiếng Anh ('en') để tiện kiểm tra
            $locale = rand(0, 1) === 0 ? 'vi' : 'en';

            if ($exists) {
                // Cập nhật trường ngôn ngữ cho bản ghi hiện tại
                DB::table('cai_dat_nguoi_dung')
                    ->where('nguoi_dung_id', $user->id)
                    ->update([
                        'ngon_ngu' => $locale,
                        'ngay_cap_nhat' => now(),
                    ]);
                $updatedCount++;
            } else {
                // Nếu chưa có cấu hình cài đặt, tạo mới bản ghi với ngôn ngữ ngẫu nhiên và dark mode mặc định là false
                DB::table('cai_dat_nguoi_dung')->insert([
                    'nguoi_dung_id' => $user->id,
                    'che_do_toi' => false,
                    'ngon_ngu' => $locale,
                    'ngay_cap_nhat' => now(),
                ]);
                $insertedCount++;
            }
        }

        $this->command->info("✅ Hoàn tất! Đã khởi tạo mới {$insertedCount} bản ghi và cập nhật {$updatedCount} bản ghi cấu hình Ngôn ngữ.");
    }
}
