<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class DatLaiMatKhauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Lấy id user
        $users = DB::table('nguoi_dung')
            ->whereIn('ten_dang_nhap', [
                'admin',
                'user_test'
            ])
            ->pluck('id', 'ten_dang_nhap');

        // Dữ liệu OTP test
        $records = [

            // OTP hợp lệ
            [
                'nguoi_dung_id' => $users['admin'] ?? 1,
                'ma_otp'        => '123456',
                'het_han'       => $now->copy()->addMinutes(15),
                'da_su_dung'    => false,
                'ngay_tao'      => $now,
            ],

            // OTP hết hạn
            [
                'nguoi_dung_id' => $users['user_test'] ?? 2,
                'ma_otp'        => '999999',
                'het_han'       => $now->copy()->subMinutes(20),
                'da_su_dung'    => false,
                'ngay_tao'      => $now->copy()->subMinutes(35),
            ],

            // OTP đã dùng
            [
                'nguoi_dung_id' => $users['admin'] ?? 1,
                'ma_otp'        => '111111',
                'het_han'       => $now->copy()->addMinutes(15),
                'da_su_dung'    => true,
                'ngay_tao'      => $now,
            ],
        ];

        DB::table('dat_lai_mat_khau')->insert($records);
    }
}
