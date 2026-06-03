<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tắt ràng buộc khóa ngoại để truncate an toàn
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('🚀 Bắt đầu quá trình seed toàn bộ cơ sở dữ liệu...');

        // 1. Tạo người dùng
        $this->command->info('👤 Đang tạo dữ liệu người dùng...');
        $this->call(NguoiDungSeeder::class);


        // 2. Tạo mối quan hệ theo dõi và gợi ý bạn bè
        $this->command->info('🤝 Đang tạo dữ liệu theo dõi và gợi ý bạn bè...');
        $this->call(TheoDoiSeeder::class);
        $this->call(GoiYBanBeSeeder::class);

        // 3. Tạo bài viết, hình ảnh, video, bình chọn, phiếu bầu
        $this->command->info('📝 Đang tạo bài viết và bình chọn...');
        $this->call(BaiVietSeeder::class);

        // 4. Tạo bình luận gốc và trả lời
        $this->command->info('💬 Đang tạo bình luận...');
        $this->call(BinhLuanSeeder::class);
        $this->call(ReplySeeders::class);

        // 5. Tạo bài viết chia sẻ
        $this->command->info('🔄 Đang tạo bài chia sẻ...');
        $this->call(SharesSeeders::class);

        // 6. Tạo media cho bình luận
        $this->command->info('🖼️ Đang tạo media cho bình luận...');
        $this->call(MediaBinhLuanSeeders::class);

        // 7. Tạo cảm xúc (like, thả tim...)
        $this->command->info('❤️ Đang tạo cảm xúc...');
        $this->call(CamXucSeeder::class);

        // 8. Tạo tin 24h (Stories)
        $this->command->info('⏱️ Đang tạo tin 24h...');
        $this->call(Tin24hSeeder::class);

        // 9. Tạo cuộc trò chuyện và tin nhắn
        $this->command->info('💬 Đang tạo tin nhắn chat...');
        $this->call(Chat1To1Seeder::class);
        $this->call(ChatGroupSeeder::class);

        // 10. Tạo thông báo
        $this->command->info('🔔 Đang tạo thông báo...');
        $this->call(ThongBaoSeeder::class);

        // 11. Tạo bài viết đã lưu
        $this->command->info('🔖 Đang tạo bài viết đã lưu...');
        $this->call(BaiVietDaLuuSeeder::class);

        // 12. Tạo cài đặt người dùng
        $this->command->info('⚙️ Đang tạo cài đặt người dùng...');
        $this->call(CaiDatNguoiDungSeeder::class);
        $this->call(DarkModeSeeder::class);
        $this->call(MultilingualSeeder::class);
        $this->call(ClearCacheSeeder::class);

        // 13. Tạo phiên đăng nhập (Quản lý thiết bị)
        $this->command->info('📱 Đang tạo phiên đăng nhập thiết bị...');
        $this->call(PhienDangNhapSeeder::class);

        // 14. Tạo dữ liệu Help Center (Trợ giúp & FAQ)
        $this->command->info('🌐 Đang tạo dữ liệu Help Center...');
        $this->call(TroGiupSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🎉 ĐÃ SEED TOÀN BỘ CƠ SỞ DỮ LIỆU THÀNH CÔNG!');
    }
}
