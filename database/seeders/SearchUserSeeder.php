<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SearchUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này khởi tạo dữ liệu người dùng mẫu phục vụ cho tính năng Tìm kiếm thành viên (Search User).
     */
    public function run(): void
    {
        $this->command->info('🔍 Bắt đầu khởi tạo dữ liệu người dùng phục vụ Tìm kiếm...');

        $now = Carbon::now();
        
        $searchUsers = [
            [
                'ten_dang_nhap'     => 'nguyenvana',
                'ten_hien_thi'      => 'Nguyễn Văn A',
                'email'             => 'nguyenvana@example.com',
                'so_dien_thoai'     => '0912345678',
                'mat_khau_hash'     => Hash::make('User@123'),
                'tieu_su'           => 'Lập trình viên đam mê công nghệ và Laravel.',
                'noi_o'             => 'Hà Nội',
                'quyen_rieng_tu'    => 'cong_khai',
                'da_xac_thuc'       => true,
                'con_hoat_dong'     => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'ten_dang_nhap'     => 'tranthib',
                'ten_hien_thi'      => 'Trần Thị B',
                'email'             => 'tranthib@example.com',
                'so_dien_thoai'     => '0987654321',
                'mat_khau_hash'     => Hash::make('User@123'),
                'tieu_su'           => 'Yêu thích chụp ảnh và khám phá du lịch ẩm thực.',
                'noi_o'             => 'Hồ Chí Minh',
                'quyen_rieng_tu'    => 'cong_khai',
                'da_xac_thuc'       => true,
                'con_hoat_dong'     => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'ten_dang_nhap'     => 'lehoangc',
                'ten_hien_thi'      => 'Lê Hoàng C',
                'email'             => 'lehoangc@example.com',
                'so_dien_thoai'     => '0905123456',
                'mat_khau_hash'     => Hash::make('User@123'),
                'tieu_su'           => 'Chuyên gia thiết kế đồ họa & giao diện người dùng.',
                'noi_o'             => 'Đà Nẵng',
                'quyen_rieng_tu'    => 'cong_khai',
                'da_xac_thuc'       => false,
                'con_hoat_dong'     => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'ten_dang_nhap'     => 'phamminhd',
                'ten_hien_thi'      => 'Phạm Minh D',
                'email'             => 'phamminhd@example.com',
                'so_dien_thoai'     => '0944123456',
                'mat_khau_hash'     => Hash::make('User@123'),
                'tieu_su'           => 'Kỹ sư hệ thống đam mê nghiên cứu khoa học.',
                'noi_o'             => 'Cần Thơ',
                'quyen_rieng_tu'    => 'cong_khai',
                'da_xac_thuc'       => true,
                'con_hoat_dong'     => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        ];

        $inserted = 0;
        foreach ($searchUsers as $u) {
            $exists = DB::table('nguoi_dung')
                ->where('ten_dang_nhap', $u['ten_dang_nhap'])
                ->orWhere('email', $u['email'])
                ->exists();
                
            if (!$exists) {
                DB::table('nguoi_dung')->insert($u);
                $inserted++;
            }
        }

        $this->command->info("✅ Hoàn tất! Đã khởi tạo thành công {$inserted} người dùng mới cho bộ máy Tìm kiếm.");
    }
}
