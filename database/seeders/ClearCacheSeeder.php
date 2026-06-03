<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearCacheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này khởi tạo hoặc cập nhật dung lượng bộ nhớ đệm (dung_luong_cache)
     * cho cấu hình cài đặt của tất cả người dùng trong hệ thống.
     */
    public function run(): void
    {
        $this->command->info('🧹 Bắt đầu khởi tạo dữ liệu bộ nhớ đệm (Clear Cache) cho người dùng...');

        $users = DB::table('nguoi_dung')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ Không tìm thấy người dùng nào trong cơ sở dữ liệu. Hãy chạy NguoiDungSeeder trước!');
            return;
        }

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($users as $user) {
            // Kiểm tra xem người dùng đã có cài đặt chưa
            $exists = DB::table('cai_dat_nguoi_dung')
                ->where('nguoi_dung_id', $user->id)
                ->exists();

            // Tạo ngẫu nhiên dung lượng bộ nhớ đệm từ 5.0 đến 350.0 MB
            $cacheSize = round((float)rand(50, 3500) / 10, 1);

            if ($exists) {
                DB::table('cai_dat_nguoi_dung')
                    ->where('nguoi_dung_id', $user->id)
                    ->update([
                        'dung_luong_cache' => $cacheSize,
                        'ngay_cap_nhat' => now(),
                    ]);
                $updatedCount++;
            } else {
                DB::table('cai_dat_nguoi_dung')->insert([
                    'nguoi_dung_id' => $user->id,
                    'che_do_toi' => false,
                    'ngon_ngu' => 'vi',
                    'dung_luong_cache' => $cacheSize,
                    'ngay_cap_nhat' => now(),
                ]);
                $insertedCount++;
            }
        }

        $this->command->info("✅ Hoàn tất! Đã khởi tạo mới {$insertedCount} bản ghi và cập nhật {$updatedCount} bản ghi dung lượng bộ nhớ đệm.");
    }
}
