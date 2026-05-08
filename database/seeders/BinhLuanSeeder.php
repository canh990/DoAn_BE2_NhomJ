<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BinhLuanSeeder extends Seeder
{
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

        // ── Lấy danh sách ID ─────────────────────────────────────────────────
        $nguoiDungIds = DB::table('nguoi_dung')->pluck('id')->toArray();
        $baiVietIds   = DB::table('bai_viet')->pluck('id')->toArray();

        $this->command->info("📊 nguoi_dung: " . count($nguoiDungIds) . " | bai_viet: " . count($baiVietIds));

        // Xóa dữ liệu cũ
        DB::table('binh_luan')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── 1. Bình luận gốc (không có cha) ──────────────────────────────────
        $recordsGoc = [];
        foreach (range(1, 30) as $i) {
            $recordsGoc[] = [
                'bai_viet_id'      => $baiVietIds[array_rand($baiVietIds)],
                'nguoi_dung_id'    => $nguoiDungIds[array_rand($nguoiDungIds)],
                'binh_luan_cha_id' => null,
                'noi_dung'         => $this->randomNoiDung(),
                'da_xoa'           => false,
                'ngay_tao'         => $this->randomTimestamp(),
                'ngay_cap_nhat'    => Carbon::now(),
            ];
        }

        foreach (array_chunk($recordsGoc, 100) as $chunk) {
            DB::table('binh_luan')->insert($chunk);
        }

        $this->command->info("  → Bình luận gốc: " . count($recordsGoc) . " bản ghi");

        // ── 2. Bình luận reply (có cha) ───────────────────────────────────────
        $binhLuanGocIds = DB::table('binh_luan')->pluck('id')->toArray();
        $replies        = [];

        foreach (range(1, 40) as $i) {
            $chaId = $binhLuanGocIds[array_rand($binhLuanGocIds)];
            $cha   = DB::table('binh_luan')->where('id', $chaId)->first();

            $replies[] = [
                'bai_viet_id'      => $cha->bai_viet_id,
                'nguoi_dung_id'    => $nguoiDungIds[array_rand($nguoiDungIds)],
                'binh_luan_cha_id' => $chaId,
                'noi_dung'         => $this->randomNoiDung(true),
                'da_xoa'           => false,
                'ngay_tao'         => $this->randomTimestamp(),
                'ngay_cap_nhat'    => Carbon::now(),
            ];
        }

        foreach (array_chunk($replies, 100) as $chunk) {
            DB::table('binh_luan')->insert($chunk);
        }

        $this->command->info("  → Bình luận reply: " . count($replies) . " bản ghi");

        $total = count($recordsGoc) + count($replies);
        $this->command->info("✅ BinhLuanSeeder hoàn tất: {$total} bản ghi.");
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function randomNoiDung(bool $isReply = false): string
    {
        $binhLuanGoc = [
            'Bài viết hay quá!',
            'Cảm ơn bạn đã chia sẻ.',
            'Thông tin rất hữu ích.',
            'Mình đồng ý với quan điểm này.',
            'Thú vị thật sự!',
            'Bạn có thể chia sẻ thêm không?',
            'Mình cũng đang gặp vấn đề tương tự.',
            'Rất hay, cảm ơn bạn nhé!',
            'Ủng hộ bạn 100%!',
            'Mình sẽ thử cách này xem sao.',
        ];

        $reply = [
            'Mình cũng nghĩ vậy!',
            'Đồng ý với bạn.',
            'Cảm ơn bạn đã phản hồi!',
            'Bạn nói đúng rồi.',
            'Haha đúng quá!',
            'Mình hiểu ý bạn rồi.',
            'Ừ, mình cũng thấy vậy.',
            'Cảm ơn bạn nhé!',
        ];

        $list = $isReply ? $reply : $binhLuanGoc;
        return $list[array_rand($list)];
    }

    private function randomTimestamp(): string
    {
        return Carbon::now()
            ->subDays(random_int(0, 365))
            ->subSeconds(random_int(0, 86400))
            ->format('Y-m-d H:i:s');
    }
}