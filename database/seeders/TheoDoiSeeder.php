<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TheoDoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('theo_doi')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = DB::table('nguoi_dung')->get();

        if ($users->count() < 2) {
            $this->command->error('Cần ít nhất 2 người dùng để tạo theo dõi. Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $this->command->info('👥 Đang tạo dữ liệu theo dõi với mật độ cao...');

        $follows = [];
        $insertedPairs = [];

        $userList = $users->values();
        $totalUsers = $userList->count();

        // Mỗi người theo dõi từ 5 đến min(12, tổng-1) người ngẫu nhiên
        $minFollow = 5;
        $maxFollow = min(12, $totalUsers - 1);

        foreach ($userList as $userA) {
            $followCount = rand($minFollow, $maxFollow);
            // Lấy danh sách người dùng khác (trừ chính mình)
            $others = $userList->where('id', '!=', $userA->id)->values();

            // Nếu số người khác ít hơn số cần theo dõi thì lấy hết
            $actualCount = min($followCount, $others->count());
            $targets = $others->random($actualCount);

            foreach ($targets as $userB) {
                $pairKey = "{$userA->id}-{$userB->id}";
                if (isset($insertedPairs[$pairKey])) {
                    continue;
                }

                // Phân bổ trạng thái đa dạng:
                // 75% da_chap_nhan, 20% cho_chap_nhan, 5% da_tu_choi
                $rand = rand(1, 100);
                if ($userB->quyen_rieng_tu === 'rieng_tu') {
                    // Tài khoản riêng tư: 50% chấp nhận, 40% chờ, 10% từ chối
                    if ($rand <= 50) {
                        $trangThai = 'da_chap_nhan';
                    } elseif ($rand <= 90) {
                        $trangThai = 'cho_chap_nhan';
                    } else {
                        $trangThai = 'da_tu_choi';
                    }
                } else {
                    // Tài khoản công khai: 80% chấp nhận, 15% chờ, 5% từ chối
                    if ($rand <= 80) {
                        $trangThai = 'da_chap_nhan';
                    } elseif ($rand <= 95) {
                        $trangThai = 'cho_chap_nhan';
                    } else {
                        $trangThai = 'da_tu_choi';
                    }
                }

                $follows[] = [
                    'nguoi_theo_doi_id'      => $userA->id,
                    'nguoi_duoc_theo_doi_id' => $userB->id,
                    'trang_thai'             => $trangThai,
                    'ngay_tao'               => Carbon::now()->subDays(rand(1, 90))->subHours(rand(0, 23)),
                ];

                $insertedPairs[$pairKey] = true;
            }
        }

        if (!empty($follows)) {
            // Insert theo chunk 500 để tránh lỗi SQL placeholder
            foreach (array_chunk($follows, 500) as $chunk) {
                DB::table('theo_doi')->insert($chunk);
            }
        }

        // Thống kê
        $total = count($follows);
        $accepted  = count(array_filter($follows, fn($f) => $f['trang_thai'] === 'da_chap_nhan'));
        $pending   = count(array_filter($follows, fn($f) => $f['trang_thai'] === 'cho_chap_nhan'));
        $rejected  = count(array_filter($follows, fn($f) => $f['trang_thai'] === 'da_tu_choi'));

        $this->command->info("✅ TheoDoiSeeder hoàn thành:");
        $this->command->info("   📊 Tổng lượt theo dõi : {$total}");
        $this->command->info("   ✔️  Đã chấp nhận       : {$accepted}");
        $this->command->info("   ⏳ Chờ chấp nhận      : {$pending}");
        $this->command->info("   ❌ Đã từ chối         : {$rejected}");
    }
}
