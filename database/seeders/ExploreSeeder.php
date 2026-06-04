<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExploreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder này khởi tạo dữ liệu bài viết mẫu chứa các hashtag đặc trưng phục vụ
     * cho tính năng Khám phá (Explore Page) và thuật toán đề xuất/xu hướng thịnh hành.
     */
    public function run(): void
    {
        $this->command->info('🌐 Bắt đầu khởi tạo dữ liệu cho tính năng Khám phá (Explore)...');

        $users = DB::table('nguoi_dung')->get();

        if ($users->isEmpty()) {
            $this->command->error('Vui lòng chạy NguoiDungSeeder trước!');
            return;
        }

        $now = Carbon::now();

        // 1. Tạo bài viết mẫu chứa các hashtag phong phú
        $postsData = [
            [
                'noi_dung' => 'Học phát triển web bằng Laravel thật tuyệt vời! Nhóm J của chúng mình đang hoàn thiện dự án cuối khóa. #laravel #nhomj #webdevelopment #code',
                'loai' => 'van_ban',
                'ten_dia_diem' => 'Hà Nội, Việt Nam',
                'cam_xuc' => 'vui_ve',
                'hoat_dong' => 'đang học tập',
            ],
            [
                'noi_dung' => 'Đà Lạt chiều nay có mưa bay nhẹ nhẹ, không khí cực kì trong lành và lãng mạn. Ai muốn đi du lịch cùng mình không? #dulich #dalat #chill #weekend',
                'loai' => 'hinh_anh',
                'ten_dia_diem' => 'Đà Lạt, Lâm Đồng',
                'cam_xuc' => 'thanh_than',
                'hoat_dong' => 'đang du lịch',
                'media_url' => 'https://picsum.photos/800/600?random=301',
            ],
            [
                'noi_dung' => 'Mới sắm chiếc bàn phím cơ và màn hình 4K mới phục vụ gõ code đêm muộn. Setup góc làm việc chill hết nấc. #setup #technology #code #chill',
                'loai' => 'hinh_anh',
                'ten_dia_diem' => 'Góc Setup',
                'cam_xuc' => 'phan_khich',
                'hoat_dong' => 'đang làm việc',
                'media_url' => 'https://picsum.photos/800/600?random=302',
            ],
            [
                'noi_dung' => 'Bắt đầu tuần mới tràn đầy năng lượng bằng bài tập yoga 30 phút và nước ép cần tây. Sức khỏe là vàng cả nhà ơi! #suckhoe #yoga #lifestyle #motivation',
                'loai' => 'van_ban',
                'ten_dia_diem' => 'Phòng tập Yoga',
                'cam_xuc' => 'khoe_khoan',
                'hoat_dong' => 'đang tập thể dục',
            ],
            [
                'noi_dung' => 'Cuối tuần tụ họp gia đình tự tay chuẩn bị món lẩu thái hải sản siêu ngon siêu cay! 🌶️🦐 #food #weekend #chill #lifestyle',
                'loai' => 'hinh_anh',
                'ten_dia_diem' => 'Nhà riêng',
                'cam_xuc' => 'hanh_phuc',
                'hoat_dong' => 'đang nấu ăn',
                'media_url' => 'https://picsum.photos/800/600?random=303',
            ],
            [
                'noi_dung' => 'Mọi hành trình vạn dặm đều bắt đầu từ một bước chân nhỏ bé. Đừng bỏ cuộc khi gặp khó khăn nhé các coder! #motivation #success #code',
                'loai' => 'van_ban',
                'ten_dia_diem' => null,
                'cam_xuc' => 'manh_me',
                'hoat_dong' => 'đang chia sẻ',
            ],
            [
                'noi_dung' => 'Laravel 11 chính thức ra mắt với nhiều thay đổi tối giản và hiệu năng vượt trội hơn. Khám phá ngay thôi! #laravel #php #technology #webdevelopment',
                'loai' => 'van_ban',
                'ten_dia_diem' => 'Văn phòng Công nghệ',
                'cam_xuc' => 'hao_huc',
                'hoat_dong' => 'đang đọc tin tức',
            ],
            [
                'noi_dung' => 'Ghé thăm phố cổ Hội An rực rỡ sắc màu đèn lồng về đêm. Cảnh vật và con người nơi đây thật thân thiện! #dulich #hoian #chill #vietnam',
                'loai' => 'hinh_anh',
                'ten_dia_diem' => 'Phố cổ Hội An, Quảng Nam',
                'cam_xuc' => 'tuyet_voi',
                'hoat_dong' => 'đang du lịch',
                'media_url' => 'https://picsum.photos/800/600?random=304',
            ]
        ];

        $seededPostIds = [];

        foreach ($postsData as $index => $postItem) {
            $randomAuthor = $users->random();

            $postId = DB::table('bai_viet')->insertGetId([
                'nguoi_dung_id' => $randomAuthor->id,
                'bai_goc_id' => null,
                'loai' => $postItem['loai'],
                'noi_dung' => $postItem['noi_dung'],
                'ten_dia_diem' => $postItem['ten_dia_diem'],
                'vi_do' => null,
                'kinh_do' => null,
                'cam_xuc' => $postItem['cam_xuc'],
                'hoat_dong' => $postItem['hoat_dong'],
                'quyen_rieng_tu' => 'cong_khai',
                'da_ghim' => false,
                'da_chinh_sua' => false,
                'da_xoa' => false,
                'created_at' => $now->copy()->subMinutes(rand(10, 1440)),
                'updated_at' => $now,
            ]);

            $seededPostIds[] = $postId;

            // Thêm hình ảnh nếu có
            if ($postItem['loai'] === 'hinh_anh' && isset($postItem['media_url'])) {
                DB::table('media_bai_viet')->insert([
                    'bai_viet_id' => $postId,
                    'loai' => 'hinh_anh',
                    'duong_dan' => $postItem['media_url'],
                    'thu_tu' => 0,
                    'ngay_tao' => $now,
                ]);
            }
        }

        // 2. Giả lập tương tác (reactions & comments) cho các bài viết Khám phá để tạo điểm xu hướng
        $this->command->info('💬 Đang giả lập cảm xúc và bình luận cho các bài viết khám phá...');
        $emoTypes = ['thich', 'tim', 'haha', 'wow'];
        $commentTemplates = [
            'Bức ảnh đẹp quá, góc chụp đỉnh ghê!',
            'Bài viết truyền cảm hứng thật sự.',
            'Quá chuẩn luôn bạn ơi!',
            'Xin địa chỉ mua setup này với ạ!',
            'Thông tin hữu ích quá, lưu lại thôi.',
            'Tuyệt vời quá cả nhà ơi!',
        ];

        foreach ($seededPostIds as $pId) {
            // Tạo 3-8 cảm xúc ngẫu nhiên từ người dùng khác nhau
            $voterUsers = $users->shuffle()->take(rand(3, 8));
            foreach ($voterUsers as $vUser) {
                DB::table('cam_xuc')->insertOrIgnore([
                    'nguoi_dung_id' => $vUser->id,
                    'bai_viet_id' => $pId,
                    'binh_luan_id' => null,
                    'loai_cam_xuc' => $emoTypes[array_rand($emoTypes)],
                    'ngay_tao' => $now->copy()->subMinutes(rand(1, 60)),
                ]);
            }

            // Tạo 1-3 bình luận ngẫu nhiên
            $commenterUsers = $users->shuffle()->take(rand(1, 3));
            foreach ($commenterUsers as $cUser) {
                DB::table('binh_luan')->insert([
                    'bai_viet_id' => $pId,
                    'nguoi_dung_id' => $cUser->id,
                    'binh_luan_cha_id' => null,
                    'noi_dung' => $commentTemplates[array_rand($commentTemplates)],
                    'da_xoa' => false,
                    'ngay_tao' => $now->copy()->subMinutes(rand(1, 60)),
                    'ngay_cap_nhat' => $now,
                ]);
            }
        }

        // 3. Tự động quét và trích xuất Hashtag từ tất cả bài viết trong database (bao gồm bài viết vừa tạo)
        $this->command->info('🏷️ Đang tự động quét và trích xuất Hashtag từ các bài viết để lập bảng xu hướng...');
        
        // Reset bảng trung gian và bảng hashtag để cập nhật chính xác
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('hashtag')->truncate();
        DB::table('bai_viet_hashtag')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $allActivePosts = DB::table('bai_viet')->where('da_xoa', false)->whereNotNull('noi_dung')->get();

        foreach ($allActivePosts as $post) {
            $content = $post->noi_dung;
            
            // Regex lấy hashtag (dạng #tagname)
            preg_match_all('/(?<=^|(?<=[^a-zA-Z0-9_\.]))#([\p{L}\p{N}_]+)/u', $content, $matches);
            
            if (!empty($matches[1])) {
                $tags = array_unique(array_map('mb_strtolower', $matches[1]));
                foreach ($tags as $tagName) {
                    // Chèn hoặc bỏ qua nếu trùng tên hashtag
                    DB::table('hashtag')->insertOrIgnore([
                        'ten' => $tagName,
                        'so_bai_viet' => 0,
                        'ngay_tao' => $now,
                    ]);
                    
                    // Lấy lại ID của hashtag vừa chèn/có sẵn
                    $tag = DB::table('hashtag')->where('ten', $tagName)->first();
                    
                    if ($tag) {
                        // Thêm vào bảng liên kết trung gian
                        DB::table('bai_viet_hashtag')->insertOrIgnore([
                            'bai_viet_id' => $post->id,
                            'hashtag_id' => $tag->id,
                        ]);
                    }
                }
            }
        }

        // 4. Cập nhật lại thuộc tính so_bai_viet cho từng hashtag
        $allHashtags = DB::table('hashtag')->get();
        $updatedHashtagsCount = 0;

        foreach ($allHashtags as $hashtag) {
            $postCount = DB::table('bai_viet_hashtag')->where('hashtag_id', $hashtag->id)->count();
            if ($postCount === 0) {
                DB::table('hashtag')->where('id', $hashtag->id)->delete();
            } else {
                DB::table('hashtag')->where('id', $hashtag->id)->update([
                    'so_bai_viet' => $postCount,
                ]);
                $updatedHashtagsCount++;
            }
        }

        $this->command->info("✅ Hoàn tất! Đã tạo thành công " . count($seededPostIds) . " bài viết khám phá mới.");
        $this->command->info("✅ Đã cập nhật chỉ mục cho {$updatedHashtagsCount} Hashtags thịnh hành!");
    }
}
