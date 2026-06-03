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
            try {
                User::factory()->count($need)->create();
            } catch (\Throwable $e) {
                return;
            }
            $users = User::query()->get();
        }

        $members = $users->values()->all();
        $groupCount = 10;
        $messagesPerGroup = 8;
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
            $size = min(6, max(3, 3 + ($i % 4)));

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
                try {
                    $conversation->members()->attach($pick->pluck('id')->all());
                } catch (\Throwable $e2) {
                    continue;
                }
            }

            // Tạo tin nhắn trong nhóm
            $senders = $pick->values()->all();
            for ($m = 1; $m <= $messagesPerGroup; $m++) {
                $sender = $senders[($m + $i) % count($senders)];
                $timeMessage = $now->copy()->subDays($groupCount - $i)->subMinutes(($messagesPerGroup - $m) * 7);

                Message::create([
                    'cuoc_tro_chuyen_id' => $conversation->id,
                    'nguoi_gui_id' => $sender->id,
                    'noi_dung' => $contents[($m + $i) % count($contents)],
                    'trang_thai' => 'sent',
                    'ngay_tao' => $timeMessage,
                ]);
            }
        }
    }
}