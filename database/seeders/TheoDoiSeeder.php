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

        $follows = [];
        $insertedPairs = [];

        foreach ($users as $userA) {
            // Mỗi người sẽ theo dõi 2 đến 4 người khác ngẫu nhiên
            $targets = $users->where('id', '!=', $userA->id)->random(rand(2, min(4, $users->count() - 1)));

            foreach ($targets as $userB) {
                $pairKey = "{$userA->id}-{$userB->id}";
                if (isset($insertedPairs[$pairKey])) {
                    continue;
                }

                // Trạng thái theo dõi: nếu userB có quyền riêng tư là 'rieng_tu', thì để trạng thái là 'cho_chap_nhan' (hoặc random 'da_chap_nhan' / 'cho_chap_nhan')
                $trangThai = 'da_chap_nhan';
                if ($userB->quyen_rieng_tu === 'rieng_tu') {
                    $trangThai = rand(0, 1) === 0 ? 'da_chap_nhan' : 'cho_chap_nhan';
                }

                $follows[] = [
                    'nguoi_theo_doi_id' => $userA->id,
                    'nguoi_duoc_theo_doi_id' => $userB->id,
                    'trang_thai' => $trangThai,
                    'ngay_tao' => Carbon::now()->subDays(rand(1, 30)),
                ];

                $insertedPairs[$pairKey] = true;
            }
        }

        if (!empty($follows)) {
            DB::table('theo_doi')->insert($follows);
        }

        $this->command->info('✅ TheoDoiSeeder: Đã tạo ' . count($follows) . ' lượt theo dõi giữa các người dùng.');
    }
}
