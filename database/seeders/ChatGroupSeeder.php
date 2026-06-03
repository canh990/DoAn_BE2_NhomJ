<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ChatGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Đảm bảo có đủ user để tạo nhóm chat.
        $users = User::query()->whereNotNull('id')->get();
        if ($users->count() < 10) {
            $need = 10 - $users->count();
            // Nếu project có factory User thì dùng; nếu không có thì chỉ skip.
            try {
                User::factory()->count($need)->create();
            } catch (\Throwable $e) {
                // Nếu factory không tồn tại, seed chat không thể chạy.
                return;
            }
            $users = User::query()->get();
        }

        $members = $users->values()->all();

        $groupCount = 10; // ít nhất 10 cuộc trò chuyện
        $messagesPerGroup = 8; // mỗi nhóm vài tin nhắn để UI có nội dung

        $now = now();

        $contents = [
            'Chào mọi người! 👋',
            'Ai rảnh tối nay chat không?',
            'Mình vừa xem xong bài viết hay quá!',
            'Test seeder cho group chat hoạt động ổn định.',
            'Bạn thấy tính năng mới thế nào?',
            'Ok mình tham gia.',
            'Gửi thêm tài liệu nhé.',
            'Cảm ơn đã chia sẻ!',
            'Mai gặp sau nha.',
            'Thử load tin nhắn realtime.',
            'Cho mình xin ý kiến với.',
        ];

        for ($i = 1; $i <= $groupCount; $i++) {
            // Chọn ngẫu nhiên 3-6 thành viên (đảm bảo khác nhau giữa group)
            $size = min(6, max(3, 3 + ($i % 4))); // 3..6

            $pick = collect($members)->shuffle()->take($size)->values();
            if ($pick->count() < 2) {
                continue;
            }

            // Tạo conversation group chat
            $conversation = Conversation::create([
                'loai' => 'group',
                'ten_nhom' => 'Nhom chat seeder #' . $i,
                'anh_nhom' => null,
            ]);

            // Gán thành viên qua pivot thanh_vien_nhom
            // Pivot có thêm cột: vai_tro, tat_thong_bao, ngay_tham_gia, doc_den_luc (theo model)
            // Nếu DB không có cột, việc attach sẽ fail; tuy nhiên thường seed cần theo schema.
            $roleFirst = 'admin';
            $pivot = [];
            foreach ($pick as $idx => $u) {
                $pivot[$u->id] = [
                    'vai_tro' => $idx === 0 ? $roleFirst : 'member',
                    'tat_thong_bao' => false,
                    'ngay_tham_gia' => $now->copy()->subDays($i)->subMinutes(10 * $idx),
                    'doc_den_luc' => $now->copy()->subDays($i),
                ];
            }

            try {
                $conversation->members()->attach($pivot);
            } catch (\Throwable $e) {
                // Nếu pivot attach fail do schema mismatch, fallback: attach đơn giản
                try {
                    $conversation->members()->attach($pick->pluck('id')->all());
                } catch (\Throwable $e2) {
                    // Không thể attach => bỏ qua group này để không ảnh hưởng chức năng khác
                    continue;
                }
            }

            // Tạo tin nhắn trong nhóm
            $senders = $pick->values()->all();
            for ($m = 1; $m <= $messagesPerGroup; $m++) {
                $sender = $senders[($m + $i) % count($senders)];

                Message::create([
                    'cuoc_tro_chuyen_id' => $conversation->id,
                    'nguoi_gui_id' => $sender->id,
                    'noi_dung' => $contents[($m + $i) % count($contents)],
                    'trang_thai' => 'sent',
                    'ngay_tao' => $now->copy()->subDays($groupCount - $i)->subMinutes(($messagesPerGroup - $m) * 7),
                ]);
            }
        }
    }
}

