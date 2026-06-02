<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BanBeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bạn bè = những người theo dõi lẫn nhau (mutual follow) với trạng thái 'da_chap_nhan'
        $users = DB::table('nguoi_dung')->get();

        if ($users->count() < 2) {
            $this->command->error('Cần ít nhất 2 người dùng để tạo bạn bè. Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $this->command->info('👥 Đang tạo dữ liệu bạn bè (theo dõi lẫn nhau)...');

        $friendships = [];
        $existingPairs = [];

        // Lấy các bản ghi hiện tại trong bảng theo_doi để tránh trùng lặp
        $existingFollows = DB::table('theo_doi')->get();
        foreach ($existingFollows as $follow) {
            $existingPairs["{$follow->nguoi_theo_doi_id}-{$follow->nguoi_duoc_theo_doi_id}"] = $follow->trang_thai;
        }

        foreach ($users as $userA) {
            // Mỗi người sẽ có từ 3 đến 6 người bạn ngẫu nhiên
            $friendCount = rand(3, min(6, $users->count() - 1));
            
            // Lấy danh sách những người bạn tiềm năng (loại trừ chính mình)
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
                    // Nếu đã có nhưng chưa chấp nhận, cập nhật thành đã chấp nhận
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
                    // Nếu đã có nhưng chưa chấp nhận, cập nhật thành đã chấp nhận
                    DB::table('theo_doi')
                        ->where('nguoi_theo_doi_id', $userB->id)
                        ->where('nguoi_duoc_theo_doi_id', $userA->id)
                        ->update(['trang_thai' => 'da_chap_nhan']);
                    $existingPairs[$pair2] = 'da_chap_nhan';
                }
            }
        }

        if (!empty($friendships)) {
            // Sử dụng chunk để tránh quá giới hạn placeholder SQL
            foreach (array_chunk($friendships, 500) as $chunk) {
                DB::table('theo_doi')->insert($chunk);
            }
        }

        $this->command->info('✅ BanBeSeeder: Đã tạo thành công ' . count($friendships) . ' mối quan hệ bạn bè mới.');
    }
}
