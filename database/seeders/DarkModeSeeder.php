<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DarkModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này dùng để khởi tạo hoặc cập nhật cấu hình giao diện Chế độ tối (Dark Mode)
     * và ngôn ngữ cho toàn bộ người dùng trong hệ thống.
     */
    public function run(): void
    {
        $this->command->info('⚙️ Bắt đầu khởi tạo dữ liệu Chế độ tối (Dark Mode) cho người dùng...');

        // Lấy danh sách toàn bộ người dung
        $users = DB::table('nguoi_dung')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ Không tìm thấy người dùng nào trong cơ sở dữ liệu. Hãy chạy NguoiDungSeeder trước!');
            return;
        }

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($users as $user) {
            // Kiểm tra xem người dùng đã có cấu hình cài đặt chưa
            $exists = DB::table('cai_dat_nguoi_dung')
                ->where('nguoi_dung_id', $user->id)
                ->exists();

            // Thiết lập ngẫu nhiên hoặc mặc định cho mục đích demo/test
            // 50% người dùng bật chế độ tối, ngôn ngữ ngẫu nhiên giữa vi và en
            $isDarkMode = (bool)rand(0, 1);
            $locale = rand(0, 1) === 0 ? 'vi' : 'en';

            if ($exists) {
                // Cập nhật cấu hình hiện tại
                DB::table('cai_dat_nguoi_dung')
                    ->where('nguoi_dung_id', $user->id)
                    ->update([
                        'che_do_toi' => $isDarkMode,
                        'ngon_ngu' => $locale,
                        'ngay_cap_nhat' => now(),
                    ]);
                $updatedCount++;
            } else {
                // Tạo mới nếu chưa có
                DB::table('cai_dat_nguoi_dung')->insert([
                    'nguoi_dung_id' => $user->id,
                    'che_do_toi' => $isDarkMode,
                    'ngon_ngu' => $locale,
                    'ngay_cap_nhat' => now(),
                ]);
                $insertedCount++;
            }
        }

        $this->command->info("✅ Hoàn tất! Đã khởi tạo mới {$insertedCount} bản ghi và cập nhật {$updatedCount} bản ghi cấu hình Dark Mode.");
    }
}
