<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CamXucSeeder extends Seeder
{
    private array $weighted = [
        'thich'   => 40,
        'tim'     => 20,
        'haha'    => 15,
        'wow'     => 10,
        'buon'    =>  9,
        'phan_no' =>  6,
    ];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ── Tự động tạo dữ liệu tạm nếu các bảng đang rỗng ──────────────────

        if (DB::table('nguoi_dung')->count() === 0) {
            $this->command->warn('⚠️  nguoi_dung rỗng → tạo 10 người dùng tạm...');
            foreach (range(1, 10) as $i) {
                DB::table('nguoi_dung')->insert([
                    'ten_dang_nhap' => "user_tam_{$i}",
                    'email'         => "user{$i}@test.com",
                    'mat_khau_hash' => bcrypt('password'),
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);
            }
        }

        if (DB::table('bai_viet')->count() === 0) {
            $this->command->warn('⚠️  bai_viet rỗng → tạo 10 bài viết tạm...');
            $ndIds = DB::table('nguoi_dung')->pluck('id')->toArray();
            foreach (range(1, 10) as $i) {
                DB::table('bai_viet')->insert([
                    'nguoi_dung_id' => $ndIds[array_rand($ndIds)],
                    'noi_dung'      => "Nội dung bài viết tạm số $i",
                    'loai'          => 'van_ban',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);
            }
        }

        if (DB::table('binh_luan')->count() === 0) {
            $this->command->warn('⚠️  binh_luan rỗng → tạo 10 bình luận tạm...');
            $ndIds = DB::table('nguoi_dung')->pluck('id')->toArray();
            $bvIds = DB::table('bai_viet')->pluck('id')->toArray();
            foreach (range(1, 10) as $i) {
                DB::table('binh_luan')->insert([
                    'nguoi_dung_id' => $ndIds[array_rand($ndIds)],
                    'bai_viet_id'   => $bvIds[array_rand($bvIds)],
                    'noi_dung'      => "Bình luận tạm số $i",
                    'ngay_tao'      => Carbon::now(),
                    'ngay_cap_nhat' => Carbon::now(),
                ]);
            }
        }

        // ── Lấy danh sách ID ─────────────────────────────────────────────────
        $nguoiDungIds = DB::table('nguoi_dung')->pluck('id')->toArray();
        $baiVietIds   = DB::table('bai_viet')->pluck('id')->toArray();
        $binhLuanIds  = DB::table('binh_luan')->pluck('id')->toArray();

        $this->command->info("📊 nguoi_dung: " . count($nguoiDungIds) . " | bai_viet: " . count($baiVietIds) . " | binh_luan: " . count($binhLuanIds));

        // Xóa dữ liệu cam_xuc cũ
        DB::table('cam_xuc')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $records   = [];
        $usedPairs = [];

        // ── 1. Cảm xúc trên bài viết ──────────────────────────────────────────
        if (!empty($baiVietIds)) {
            $target      = min(count($baiVietIds) * random_int(3, 7), count($nguoiDungIds) * count($baiVietIds), 300);
            $attempts    = 0;
            $maxAttempts = $target * 5;

            while (count($records) < $target && $attempts < $maxAttempts) {
                $attempts++;
                $ndId = $nguoiDungIds[array_rand($nguoiDungIds)];
                $bvId = $baiVietIds[array_rand($baiVietIds)];
                $key  = "nd_{$ndId}_bv_{$bvId}";

                if (isset($usedPairs[$key])) continue;

                $usedPairs[$key] = true;
                $records[] = [
                    'nguoi_dung_id' => $ndId,
                    'bai_viet_id'   => $bvId,
                    'binh_luan_id'  => null,
                    'loai_cam_xuc'  => $this->randomCamXuc(),
                    'ngay_tao'      => $this->randomTimestamp(),
                ];
            }

            $this->command->info("  → Cảm xúc bài viết:  " . count($records) . " bản ghi");
        }

        // ── 2. Cảm xúc trên bình luận ─────────────────────────────────────────
        if (!empty($binhLuanIds)) {
            $countBefore = count($records);
            $target      = min(count($binhLuanIds) * random_int(2, 5), count($nguoiDungIds) * count($binhLuanIds), 200);
            $attempts    = 0;
            $maxAttempts = $target * 5;

            while ((count($records) - $countBefore) < $target && $attempts < $maxAttempts) {
                $attempts++;
                $ndId = $nguoiDungIds[array_rand($nguoiDungIds)];
                $blId = $binhLuanIds[array_rand($binhLuanIds)];
                $key  = "nd_{$ndId}_bl_{$blId}";

                if (isset($usedPairs[$key])) continue;

                $usedPairs[$key] = true;
                $records[] = [
                    'nguoi_dung_id' => $ndId,
                    'bai_viet_id'   => null,
                    'binh_luan_id'  => $blId,
                    'loai_cam_xuc'  => $this->randomCamXuc(),
                    'ngay_tao'      => $this->randomTimestamp(),
                ];
            }

            $this->command->info("  → Cảm xúc bình luận: " . (count($records) - $countBefore) . " bản ghi");
        }

        // ── 3. Insert theo batch ───────────────────────────────────────────────
        foreach (array_chunk($records, 100) as $chunk) {
            DB::table('cam_xuc')->insert($chunk);
        }

        $this->command->info('✅ CamXucSeeder hoàn tất: ' . count($records) . ' bản ghi.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function randomCamXuc(): string
    {
        $rand       = random_int(1, 100);
        $cumulative = 0;

        foreach ($this->weighted as $loai => $trongSo) {
            $cumulative += $trongSo;
            if ($rand <= $cumulative) {
                return $loai;
            }
        }

        return 'thich';
    }

    private function randomTimestamp(): string
    {
        return Carbon::now()
            ->subDays(random_int(0, 365))
            ->subSeconds(random_int(0, 86400))
            ->format('Y-m-d H:i:s');
    }
}