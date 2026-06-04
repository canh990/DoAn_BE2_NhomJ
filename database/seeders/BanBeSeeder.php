<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BanBeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Bạn bè = những người theo dõi lẫn nhau (mutual follow) với trạng thái 'da_chap_nhan'.
     * Seeder này bổ sung thêm các cặp bạn bè (2 chiều) vào bảng theo_doi,
     * đảm bảo mỗi người dùng có đủ số bạn bè phong phú.
     */
    public function run(): void
    {
        $users = DB::table('nguoi_dung')->get();

        if ($users->count() < 2) {
            $this->command->error('Cần ít nhất 2 người dùng để tạo bạn bè. Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $this->command->info('👥 Đang tạo dữ liệu bạn bè (theo dõi lẫn nhau)...');

        $friendships = [];
        $existingPairs = [];

        // Load toàn bộ theo_doi hiện tại để tránh trùng
        $existingFollows = DB::table('theo_doi')->get();
        foreach ($existingFollows as $follow) {
            $existingPairs["{$follow->nguoi_theo_doi_id}-{$follow->nguoi_duoc_theo_doi_id}"] = $follow->trang_thai;
        }

        $userList = $users->values();
        $totalUsers = $userList->count();

        // Mỗi user sẽ có tối thiểu 6 bạn, tối đa min(15, total-1) bạn
        $minFriends = 6;
        $maxFriends = min(15, $totalUsers - 1);

        // -----------------------------------------------------------
        // Bước 1: Nhóm "clique" cố định – admin và user_test chắc chắn
        //         có quan hệ bạn bè với TẤT CẢ người dùng công khai khác
        // -----------------------------------------------------------
        $adminUser     = $userList->firstWhere('ten_dang_nhap', 'admin');
        $userTest      = $userList->firstWhere('ten_dang_nhap', 'user_test');
        $publicUsers   = $userList->where('quyen_rieng_tu', 'cong_khai')
                                  ->where('con_hoat_dong', true)
                                  ->values();

        $vipPairs = [];
        if ($adminUser && $userTest) {
            // admin ↔ user_test
            $vipPairs[] = [$adminUser, $userTest];

            // admin ↔ tất cả public user
            foreach ($publicUsers as $pu) {
                if ($pu->id !== $adminUser->id) {
                    $vipPairs[] = [$adminUser, $pu];
                }
            }

            // user_test ↔ tất cả public user
            foreach ($publicUsers as $pu) {
                if ($pu->id !== $userTest->id) {
                    $vipPairs[] = [$userTest, $pu];
                }
            }
        }

        $insertOrUpdate = function (int $aId, int $bId, Carbon $date) use (&$friendships, &$existingPairs) {
            $p1 = "{$aId}-{$bId}";
            $p2 = "{$bId}-{$aId}";

            // Chiều A → B
            if (!isset($existingPairs[$p1])) {
                $friendships[] = [
                    'nguoi_theo_doi_id'      => $aId,
                    'nguoi_duoc_theo_doi_id' => $bId,
                    'trang_thai'             => 'da_chap_nhan',
                    'ngay_tao'               => $date,
                ];
                $existingPairs[$p1] = 'da_chap_nhan';
            } elseif ($existingPairs[$p1] !== 'da_chap_nhan') {
                DB::table('theo_doi')
                    ->where('nguoi_theo_doi_id', $aId)
                    ->where('nguoi_duoc_theo_doi_id', $bId)
                    ->update(['trang_thai' => 'da_chap_nhan']);
                $existingPairs[$p1] = 'da_chap_nhan';
            }

            // Chiều B → A
            if (!isset($existingPairs[$p2])) {
                $friendships[] = [
                    'nguoi_theo_doi_id'      => $bId,
                    'nguoi_duoc_theo_doi_id' => $aId,
                    'trang_thai'             => 'da_chap_nhan',
                    'ngay_tao'               => $date,
                ];
                $existingPairs[$p2] = 'da_chap_nhan';
            } elseif ($existingPairs[$p2] !== 'da_chap_nhan') {
                DB::table('theo_doi')
                    ->where('nguoi_theo_doi_id', $bId)
                    ->where('nguoi_duoc_theo_doi_id', $aId)
                    ->update(['trang_thai' => 'da_chap_nhan']);
                $existingPairs[$p2] = 'da_chap_nhan';
            }
        };

        // Chèn các cặp VIP
        foreach ($vipPairs as [$uA, $uB]) {
            $date = Carbon::now()->subDays(rand(30, 180));
            $insertOrUpdate($uA->id, $uB->id, $date);
        }

        // -----------------------------------------------------------
        // Bước 2: Mỗi người dùng ngẫu nhiên thêm bạn bè đến đủ số
        //         min..max người bạn (2 chiều).
        // -----------------------------------------------------------
        foreach ($userList as $userA) {
            // Đếm số bạn hiện tại của userA (mutual = 2 chiều đều da_chap_nhan)
            $currentFriendCount = collect($existingPairs)
                ->filter(fn($status, $pair) =>
                    $status === 'da_chap_nhan' &&
                    (
                        str_starts_with($pair, "{$userA->id}-") ||
                        str_ends_with($pair, "-{$userA->id}")
                    )
                )
                ->keys()
                ->map(fn($pair) => explode('-', $pair))
                ->filter(fn($parts) => (int)$parts[0] === $userA->id || (int)$parts[1] === $userA->id)
                ->count();

            if ($currentFriendCount >= $maxFriends) {
                continue; // Đã đủ bạn
            }

            $needed = rand(
                max(0, $minFriends - $currentFriendCount),
                max(0, $maxFriends - $currentFriendCount)
            );

            if ($needed <= 0) {
                continue;
            }

            // Lọc những người chưa là bạn bè
            $candidates = $userList->filter(function ($u) use ($userA, $existingPairs) {
                if ($u->id === $userA->id) return false;
                $p1 = "{$userA->id}-{$u->id}";
                $p2 = "{$u->id}-{$userA->id}";
                // Chỉ lấy người chưa có quan hệ 2 chiều đã chấp nhận
                $mutual = (isset($existingPairs[$p1]) && $existingPairs[$p1] === 'da_chap_nhan')
                       && (isset($existingPairs[$p2]) && $existingPairs[$p2] === 'da_chap_nhan');
                return !$mutual;
            })->values();

            if ($candidates->isEmpty()) {
                continue;
            }

            $actualNeeded = min($needed, $candidates->count());
            $chosen = $candidates->random($actualNeeded);

            foreach ($chosen as $userB) {
                $date = Carbon::now()->subDays(rand(1, 60))->subHours(rand(0, 23));
                $insertOrUpdate($userA->id, $userB->id, $date);
            }
        }

        // -----------------------------------------------------------
        // Bước 3: Tạo thêm các cặp bạn bè ngẫu nhiên hoàn toàn (bonus)
        //         để đạt mật độ mạng lưới xã hội tốt hơn
        // -----------------------------------------------------------
        $bonusPairs = 0;
        $maxBonus = (int)($totalUsers * 3); // thêm tối đa 3x users quan hệ bonus

        for ($i = 0; $i < $maxBonus; $i++) {
            $uA = $userList->random();
            $uB = $userList->filter(fn($u) => $u->id !== $uA->id)->random();

            $date = Carbon::now()->subDays(rand(1, 120))->subHours(rand(0, 23));

            $p1 = "{$uA->id}-{$uB->id}";
            $p2 = "{$uB->id}-{$uA->id}";

            $alreadyMutual = (isset($existingPairs[$p1]) && $existingPairs[$p1] === 'da_chap_nhan')
                          && (isset($existingPairs[$p2]) && $existingPairs[$p2] === 'da_chap_nhan');

            if (!$alreadyMutual) {
                $insertOrUpdate($uA->id, $uB->id, $date);
                $bonusPairs++;
            }
        }

        // -----------------------------------------------------------
        // Insert batch vào DB
        // -----------------------------------------------------------
        if (!empty($friendships)) {
            foreach (array_chunk($friendships, 500) as $chunk) {
                DB::table('theo_doi')->insert($chunk);
            }
        }

        $total = count($friendships);
        $this->command->info("✅ BanBeSeeder hoàn thành:");
        $this->command->info("   🤝 Tổng cặp quan hệ mới inserted : {$total}");
        $this->command->info("   🎁 Bonus pairs thêm              : {$bonusPairs}");
        $this->command->info("   📊 Tổng record trong theo_doi     : " . DB::table('theo_doi')->count());
    }
}
