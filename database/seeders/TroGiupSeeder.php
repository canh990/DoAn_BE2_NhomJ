<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TroGiupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌐 Bắt đầu khởi tạo dữ liệu cho Help Center (tro_giup)...');

        // Truncate table trước khi seed để tránh trùng lặp
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tro_giup')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Dữ liệu Thông tin liên hệ và Giới thiệu hệ thống
        $infoData = [
            [
                'loai' => 'info',
                'khoa' => 'email',
                'cau_hoi' => null,
                'tra_loi' => 'support@nhomj.vn',
                'ngon_ngu' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'info',
                'khoa' => 'hotline',
                'cau_hoi' => null,
                'tra_loi' => '1900 1111',
                'ngon_ngu' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'info',
                'khoa' => 'zalo_group',
                'cau_hoi' => null,
                'tra_loi' => 'https://zalo.me/g/nhomhotroIT',
                'ngon_ngu' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'info',
                'khoa' => 'app_name',
                'cau_hoi' => null,
                'tra_loi' => 'Hệ thống Mạng xã hội nội bộ NHOMJ',
                'ngon_ngu' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'info',
                'khoa' => 'version',
                'cau_hoi' => null,
                'tra_loi' => 'v2.1.0',
                'ngon_ngu' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'info',
                'khoa' => 'release_date',
                'cau_hoi' => null,
                'tra_loi' => '02/06/2026',
                'ngon_ngu' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'info',
                'khoa' => 'company',
                'cau_hoi' => null,
                'tra_loi' => 'Công ty Cổ phần Công nghệ NHOMJ',
                'ngon_ngu' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tro_giup')->insert($infoData);
        $this->command->info('✅ Đã khởi tạo cấu hình liên hệ và giới thiệu hệ thống.');

        // 2. Dữ liệu câu hỏi FAQ (Tiếng Việt)
        $faqVi = [
            [
                'loai' => 'faq',
                'khoa' => null,
                'cau_hoi' => 'Làm thế nào để báo cáo nội dung vi phạm?',
                'tra_loi' => 'Để báo cáo vi phạm, bạn bấm vào nút ba chấm ở góc trên bài viết hoặc bình luận và chọn \'Báo cáo vi phạm\'.',
                'ngon_ngu' => 'vi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'faq',
                'khoa' => null,
                'cau_hoi' => 'Tôi bị mất tài khoản, phải làm sao?',
                'tra_loi' => 'Vui lòng sử dụng tính năng \'Quên mật khẩu\' hoặc gửi email trực tiếp tới support@nhomj.vn kèm theo giấy tờ định danh.',
                'ngon_ngu' => 'vi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'faq',
                'khoa' => null,
                'cau_hoi' => 'Tôi quên mật khẩu, cách lấy lại?',
                'tra_loi' => 'Bạn có thể nhấp vào liên kết \'Quên mật khẩu\' ở trang đăng nhập để nhận mã OTP khôi phục.',
                'ngon_ngu' => 'vi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'faq',
                'khoa' => null,
                'cau_hoi' => 'Cách bật bảo mật 2FA?',
                'tra_loi' => 'Truy cập phần Cài đặt tài khoản > Bảo mật và bật tính năng \'Xác thực 2 yếu tố (2FA)\'.',
                'ngon_ngu' => 'vi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tro_giup')->insert($faqVi);
        $this->command->info('✅ Đã khởi tạo bộ FAQ Tiếng Việt (4 câu).');

        // 3. Dữ liệu câu hỏi FAQ (Tiếng Anh - English)
        $faqEn = [
            [
                'loai' => 'faq',
                'khoa' => null,
                'cau_hoi' => 'How to report a policy violation?',
                'tra_loi' => 'To report a violation, click on the three-dot button in the upper right corner of the post or comment and select \'Report violation\'.',
                'ngon_ngu' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'faq',
                'khoa' => null,
                'cau_hoi' => 'I lost my account, what should I do?',
                'tra_loi' => 'Please use the \'Forgot Password\' feature or send an email directly to support@nhomj.vn with your identification documents.',
                'ngon_ngu' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'faq',
                'khoa' => null,
                'cau_hoi' => 'I forgot my password, how to retrieve it?',
                'tra_loi' => 'You can click on the \'Forgot Password\' link on the login page to receive a recovery OTP.',
                'ngon_ngu' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'loai' => 'faq',
                'khoa' => null,
                'cau_hoi' => 'How to enable 2FA security?',
                'tra_loi' => 'Go to Account Settings > Security and enable the \'Two-Factor Authentication (2FA)\' feature.',
                'ngon_ngu' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tro_giup')->insert($faqEn);
        $this->command->info('✅ Đã khởi tạo bộ FAQ Tiếng Anh (4 câu).');
        
        $this->command->info('🎉 Hoàn thành seed dữ liệu Help Center thành công!');
    }
}
