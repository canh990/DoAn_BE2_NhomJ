<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GoiYBanBeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('nguoi_dung')->where('con_hoat_dong', true)->get();

        if ($users->count() < 3) {
            $this->command->error('Cần ít nhất 3 người dùng hoạt động để tạo gợi ý bạn bè. Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $this->command->info('👥 Đang tạo dữ liệu mối quan hệ bạn bè để hiển thị gợi ý (Friend Suggestions)...');

        $friendships = [];
        $existingPairs = [];

        // Lấy các bản ghi hiện tại trong bảng theo_doi để tránh trùng lặp
        $existingFollows = DB::table('theo_doi')->get();
        foreach ($existingFollows as $follow) {
            $existingPairs["{$follow->nguoi_theo_doi_id}-{$follow->nguoi_duoc_theo_doi_id}"] = $follow->trang_thai;
        }

        // Tạo chuỗi quan hệ bạn bè (mutual follow) để tạo "bạn chung" (mutual friends)
        // Ví dụ: User A <=> User B và User B <=> User C. Nhưng A và C chưa theo dõi nhau.
        // Khi đó, C sẽ được gợi ý cho A với lý do "Có bạn chung" (User B).
        foreach ($users as $userA) {
            // Mỗi người dùng kết bạn (theo dõi hai chiều) với 2-4 người dùng khác ngẫu nhiên
            $friendCount = rand(2, min(4, $users->count() - 1));
            $potentialFriends = $users->where('id', '!=', $userA->id)->random($friendCount);

            foreach ($potentialFriends as $userB) {
                $pair1 = "{$userA->id}-{$userB->id}";
                $pair2 = "{$userB->id}-{$userA->id}";

                $now = Carbon::now()->subDays(rand(1, 30));

                // Chiều 1: User A theo dõi User B
                if (!isset($existingPairs[$pair1])) {
                    $friendships[] = [
                        'nguoi_theo_doi_id' => $userA->id,
                        'nguoi_duoc_theo_doi_id' => $userB->id,
                        'trang_thai' => 'da_chap_nhan',
                        'ngay_tao' => $now,
                    ];
                    $existingPairs[$pair1] = 'da_chap_nhan';
                } elseif ($existingPairs[$pair1] !== 'da_chap_nhan') {
                    DB::table('theo_doi')
                        ->where('nguoi_theo_doi_id', $userA->id)
                        ->where('nguoi_duoc_theo_doi_id', $userB->id)
                        ->update(['trang_thai' => 'da_chap_nhan']);
                    $existingPairs[$pair1] = 'da_chap_nhan';
                }

                // Chiều 2: User B theo dõi User A
                if (!isset($existingPairs[$pair2])) {
                    $friendships[] = [
                        'nguoi_theo_doi_id' => $userB->id,
                        'nguoi_duoc_theo_doi_id' => $userA->id,
                        'trang_thai' => 'da_chap_nhan',
                        'ngay_tao' => $now,
                    ];
                    $existingPairs[$pair2] = 'da_chap_nhan';
                } elseif ($existingPairs[$pair2] !== 'da_chap_nhan') {
                    DB::table('theo_doi')
                        ->where('nguoi_theo_doi_id', $userB->id)
                        ->where('nguoi_duoc_theo_doi_id', $userA->id)
                        ->update(['trang_thai' => 'da_chap_nhan']);
                    $existingPairs[$pair2] = 'da_chap_nhan';
                }
            }
        }

        // Chèn các mối quan hệ bạn bè mới vào DB
        if (!empty($friendships)) {
            foreach (array_chunk($friendships, 200) as $chunk) {
                DB::table('theo_doi')->insert($chunk);
            }
        }

        $this->command->info('✅ GoiYBanBeSeeder: Khởi tạo thành công ' . count($friendships) . ' mối quan hệ bạn bè (theo dõi 2 chiều).');
    }
}
