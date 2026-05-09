<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            [
                'ten_dang_nhap'     => 'admin',
                'email'             => 'admin@example.com',
                'so_dien_thoai'     => '0901000001',
                'mat_khau_hash'     => Hash::make('Admin@123'),
                'anh_dai_dien'      => null,
                'anh_bia'           => null,
                'tieu_su'           => 'Quản trị viên hệ thống.',
                'ngay_sinh'         => '1990-01-01',
                'noi_o'             => 'Hà Nội',
                'quyen_rieng_tu'    => 'cong_khai',
                'da_xac_thuc'       => true,
                'con_hoat_dong'     => true,
                'nha_cung_cap_oauth'=> null,
                'id_oauth'          => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'ten_dang_nhap'     => 'user_test',
                'email'             => 'user@example.com',
                'so_dien_thoai'     => '0901000002',
                'mat_khau_hash'     => Hash::make('User@123'),
                'anh_dai_dien'      => null,
                'anh_bia'           => null,
                'tieu_su'           => 'Tài khoản test thông thường.',
                'ngay_sinh'         => '1995-06-15',
                'noi_o'             => 'Hồ Chí Minh',
                'quyen_rieng_tu'    => 'cong_khai',
                'da_xac_thuc'       => true,
                'con_hoat_dong'     => true,
                'nha_cung_cap_oauth'=> null,
                'id_oauth'          => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'ten_dang_nhap'     => 'nguoi_dung_private',
                'email'             => 'private@example.com',
                'so_dien_thoai'     => '0901000003',
                'mat_khau_hash'     => Hash::make('Private@123'),
                'anh_dai_dien'      => null,
                'anh_bia'           => null,
                'tieu_su'           => null,
                'ngay_sinh'         => null,
                'noi_o'             => null,
                'quyen_rieng_tu'    => 'rieng_tu',
                'da_xac_thuc'       => false,
                'con_hoat_dong'     => true,
                'nha_cung_cap_oauth'=> null,
                'id_oauth'          => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'ten_dang_nhap'     => 'google_user',
                'email'             => 'oauth@gmail.com',
                'so_dien_thoai'     => null,
                'mat_khau_hash'     => null,
                'anh_dai_dien'      => 'https://lh3.googleusercontent.com/a/default',
                'anh_bia'           => null,
                'tieu_su'           => 'Đăng nhập qua Google.',
                'ngay_sinh'         => null,
                'noi_o'             => 'Đà Nẵng',
                'quyen_rieng_tu'    => 'cong_khai',
                'da_xac_thuc'       => true,
                'con_hoat_dong'     => true,
                'nha_cung_cap_oauth'=> 'google',
                'id_oauth'          => '109876543210987654321',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'ten_dang_nhap'     => 'inactive_user',
                'email'             => 'inactive@example.com',
                'so_dien_thoai'     => '0901000005',
                'mat_khau_hash'     => Hash::make('Inactive@123'),
                'anh_dai_dien'      => null,
                'anh_bia'           => null,
                'tieu_su'           => 'Tài khoản đã bị vô hiệu hoá.',
                'ngay_sinh'         => '1988-03-20',
                'noi_o'             => 'Cần Thơ',
                'quyen_rieng_tu'    => 'cong_khai',
                'da_xac_thuc'       => true,
                'con_hoat_dong'     => false,
                'nha_cung_cap_oauth'=> null,
                'id_oauth'          => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        // Chỉ insert những user chưa tồn tại
        $existingUsernames = DB::table('nguoi_dung')->pluck('ten_dang_nhap')->toArray();
        $existingEmails = DB::table('nguoi_dung')->whereNotNull('email')->pluck('email')->toArray();
        $existingPhones = DB::table('nguoi_dung')->whereNotNull('so_dien_thoai')->pluck('so_dien_thoai')->toArray();

        $usersToInsert = [];
        foreach ($users as $user) {
            $usernameExists = in_array($user['ten_dang_nhap'], $existingUsernames);
            $emailExists = $user['email'] && in_array($user['email'], $existingEmails);
            $phoneExists = $user['so_dien_thoai'] && in_array($user['so_dien_thoai'], $existingPhones);

            if (!$usernameExists && !$emailExists && !$phoneExists) {
                $usersToInsert[] = $user;
            }
        }

        if (!empty($usersToInsert)) {
            DB::table('nguoi_dung')->insert($usersToInsert);
        }
    }
}