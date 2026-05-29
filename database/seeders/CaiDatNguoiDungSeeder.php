<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaiDatNguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('cai_dat_nguoi_dung')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = DB::table('nguoi_dung')->get();

        if ($users->isEmpty()) {
            $this->command->error('Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $settings = [];
        foreach ($users as $user) {
            $settings[] = [
                'nguoi_dung_id' => $user->id,
                'che_do_toi' => (bool)rand(0, 1),
                'ngon_ngu' => rand(0, 4) === 0 ? 'en' : 'vi',
            ];
        }

        DB::table('cai_dat_nguoi_dung')->insert($settings);
        $this->command->info('✅ CaiDatNguoiDungSeeder: Đã cấu hình cài đặt cho ' . count($settings) . ' người dùng.');
    }
}
