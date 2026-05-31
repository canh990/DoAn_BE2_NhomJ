<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhienDangNhapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này dùng để tạo dữ liệu mẫu cho danh sách phiên đăng nhập / thiết bị (Device Management)
     * của người dùng trong hệ thống để test tính năng đăng xuất từ xa và quản lý phiên.
     */
    public function run(): void
    {
        $this->command->info('📱 Bắt đầu khởi tạo dữ liệu phiên đăng nhập và quản lý thiết bị...');

        // Tắt khóa ngoại để truncate an toàn
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('phien_dang_nhap')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Lấy danh sách toàn bộ người dùng
        $users = DB::table('nguoi_dung')->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ Không tìm thấy người dùng nào trong cơ sở dữ liệu. Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        // Danh sách thiết bị mẫu đa dạng để hiển thị giao diện đẹp mắt
        $devices = [
            [
                'ten_thiet_bi' => 'Windows PC',
                'trinh_duyet' => 'Chrome 120.0',
                'he_dieu_hanh' => 'Windows 11',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ],
            [
                'ten_thiet_bi' => 'MacBook Pro',
                'trinh_duyet' => 'Safari 17.0',
                'he_dieu_hanh' => 'MacOS Sonoma',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            ],
            [
                'ten_thiet_bi' => 'iPhone 15 Pro',
                'trinh_duyet' => 'Safari Mobile',
                'he_dieu_hanh' => 'iPhone (iOS 17.2)',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/605.1.15',
            ],
            [
                'ten_thiet_bi' => 'Samsung Galaxy S24',
                'trinh_duyet' => 'Chrome Mobile',
                'he_dieu_hanh' => 'Android 14',
                'user_agent' => 'Mozilla/5.0 (Linux; Android 14; SM-S928B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Mobile Safari/537.36',
            ],
            [
                'ten_thiet_bi' => 'Linux PC',
                'trinh_duyet' => 'Firefox 119.0',
                'he_dieu_hanh' => 'Linux (Ubuntu 22.04)',
                'user_agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0',
            ],
            [
                'ten_thiet_bi' => 'iPad Air',
                'trinh_duyet' => 'Safari Mobile',
                'he_dieu_hanh' => 'iPad (iPadOS 17.1)',
                'user_agent' => 'Mozilla/5.0 (iPad; CPU OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/605.1.15',
            ],
        ];

        // Danh sách IP mẫu
        $ips = [
            '127.0.0.1',
            '192.168.1.15',
            '113.161.45.22',
            '14.161.22.89',
            '171.244.12.110',
            '115.79.138.4',
            '27.72.90.154'
        ];

        $insertedCount = 0;

        foreach ($users as $user) {
            // Mỗi người dùng sẽ có từ 2 đến 4 phiên đăng nhập khác nhau để tiện test giao diện cuộn & quản lý
            $sessionCount = rand(2, 4);
            
            // Xáo trộn danh sách thiết bị để lấy ngẫu nhiên
            $userDevices = $devices;
            shuffle($userDevices);

            for ($i = 0; $i < $sessionCount; $i++) {
                $deviceInfo = $userDevices[$i];
                
                // Phiên đầu tiên (index 0) luôn là phiên hiện tại để chắc chắn mỗi user luôn có ít nhất 1 phiên hiện tại đang dùng
                $isCurrentSession = ($i === 0);
                
                // Trạng thái đã đăng xuất (chỉ áp dụng cho các phiên phụ, tỉ lệ 30% đã đăng xuất)
                $loggedOutAt = null;
                if (!$isCurrentSession && rand(1, 10) <= 3) {
                    $loggedOutAt = now()->subMinutes(rand(10, 10000));
                }

                // Thời gian hoạt động cuối
                $lastActive = $isCurrentSession 
                    ? now() 
                    : now()->subMinutes(rand(5, 43200)); // Trong vòng 30 ngày trước

                DB::table('phien_dang_nhap')->insert([
                    'nguoi_dung_id' => $user->id,
                    'thong_tin_thiet_bi' => $deviceInfo['ten_thiet_bi'] . ' (' . $deviceInfo['he_dieu_hanh'] . ')',
                    'ten_thiet_bi' => $deviceInfo['ten_thiet_bi'],
                    'trinh_duyet' => $deviceInfo['trinh_duyet'],
                    'he_dieu_hanh' => $deviceInfo['he_dieu_hanh'],
                    'user_agent' => $deviceInfo['user_agent'],
                    'dia_chi_ip' => $ips[array_rand($ips)],
                    'lan_hoat_dong_cuoi' => $lastActive,
                    'dang_xuat_luc' => $loggedOutAt,
                    'la_phien_hien_tai' => $isCurrentSession,
                    'token_hash' => hash('sha256', Str::random(64)),
                    'het_han' => now()->addDays(rand(1, 30)),
                    'ngay_tao' => $lastActive->copy()->subHours(rand(1, 24)),
                ]);

                $insertedCount++;
            }
        }

        $this->command->info("✅ Hoàn tất! Đã khởi tạo thành công {$insertedCount} phiên đăng nhập mẫu cho các thiết bị.");
    }
}
