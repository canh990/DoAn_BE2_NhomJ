<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Chat1To1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Get users for the 1-to-1 conversation
        $user1 = User::where('ten_dang_nhap', 'admin')->first();
        $user2 = User::where('ten_dang_nhap', 'user_test')->first();

        if (!$user1 || !$user2) {
            $this->command->error('Required users (admin, user_test) not found. Please run NguoiDungSeeder first.');
            return;
        }

        // Check if conversation already exists between these two users
        $existingConversation = Conversation::whereHas('members', function ($query) use ($user1) {
            $query->where('nguoi_dung_id', $user1->id);
        })->whereHas('members', function ($query) use ($user2) {
            $query->where('nguoi_dung_id', $user2->id);
        })->where('loai', 'ca_nhan')->first();

        if ($existingConversation) {
            $this->command->warn('Conversation between admin and user_test already exists. Skipping.');
            return;
        }

        // Create 1-to-1 conversation
        $conversation = Conversation::create([
            'loai' => 'ca_nhan',
            'ten_nhom' => null,
            'anh_nhom' => null,
            'ngay_tao' => $now,
            'ngay_cap_nhat' => $now,
        ]);

        // Add members to conversation
        $conversation->members()->attach(
            [
                $user1->id => [
                    'vai_tro' => 'thanh_vien',
                    'tat_thong_bao' => false,
                    'ngay_tham_gia' => $now,
                    'doc_den_luc' => $now,
                ],
                $user2->id => [
                    'vai_tro' => 'thanh_vien',
                    'tat_thong_bao' => false,
                    'ngay_tham_gia' => $now,
                    'doc_den_luc' => $now,
                ],
            ]
        );

        // Create sample messages
        $messages = [
            [
                'cuoc_tro_chuyen_id' => $conversation->id,
                'nguoi_gui_id' => $user1->id,
                'noi_dung' => 'Xin chào! Đây là tin nhắn đầu tiên của bạn.',
                'trang_thai' => 'da_xem',
                'da_thu_hoi' => false,
                'ngay_tao' => $now->copy()->subMinutes(10),
            ],
            [
                'cuoc_tro_chuyen_id' => $conversation->id,
                'nguoi_gui_id' => $user2->id,
                'noi_dung' => 'Xin chào! Cảm ơn bạn đã gửi tin nhắn.',
                'trang_thai' => 'da_xem',
                'da_thu_hoi' => false,
                'ngay_tao' => $now->copy()->subMinutes(8),
            ],
            [
                'cuoc_tro_chuyen_id' => $conversation->id,
                'nguoi_gui_id' => $user1->id,
                'noi_dung' => 'Bạn khỏe không?',
                'trang_thai' => 'da_xem',
                'da_thu_hoi' => false,
                'ngay_tao' => $now->copy()->subMinutes(5),
            ],
            [
                'cuoc_tro_chuyen_id' => $conversation->id,
                'nguoi_gui_id' => $user2->id,
                'noi_dung' => 'Tôi khỏe lắm! Còn bạn?',
                'trang_thai' => 'da_nhan',
                'da_thu_hoi' => false,
                'ngay_tao' => $now->copy()->subMinutes(2),
            ],
            [
                'cuoc_tro_chuyen_id' => $conversation->id,
                'nguoi_gui_id' => $user1->id,
                'noi_dung' => 'Tôi cũng khỏe!',
                'trang_thai' => 'da_gui',
                'da_thu_hoi' => false,
                'ngay_tao' => $now,
            ],
        ];

        DB::table('tin_nhan')->insert($messages);

        $this->command->info('Chat1-to-1 seeder completed successfully!');
        $this->command->info("Conversation ID: {$conversation->id}");
        $this->command->info("Members: {$user1->ten_dang_nhap} <-> {$user2->ten_dang_nhap}");
        $this->command->info('Total messages: ' . count($messages));
    }
}
