<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BaiViet;
use App\Models\BinhLuan;
use App\Models\ThongBao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThongBaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('thong_bao')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = DB::table('nguoi_dung')->pluck('id')->toArray();
        $posts = DB::table('bai_viet')->get(['id', 'nguoi_dung_id'])->toArray();
        $comments = DB::table('binh_luan')->get(['id', 'nguoi_dung_id', 'bai_viet_id'])->toArray();

        if (empty($users) || empty($posts)) {
            $this->command->warn('⚠️  NguoiDung hoặc BaiViet trống. Vui lòng chạy các seeder khác trước.');
            return;
        }

        $notifications = [];
        $types = ['thich', 'binh_luan', 'tag', 'nhan_den', 'theo_doi', 'chia_se'];

        // Tạo khoảng 100 thông báo ngẫu nhiên
        for ($i = 0; $i < 100; $i++) {
            $loai = $types[array_rand($types)];
            $nguoiThucHienId = $users[array_rand($users)];
            
            $nguoiDungId = null;
            $baiVietId = null;
            $binhLuanId = null;

            switch ($loai) {
                case 'thich':
                case 'chia_se':
                case 'tag':
                case 'nhan_den':
                    $post = $posts[array_rand($posts)];
                    $baiVietId = $post->id;
                    $nguoiDungId = $post->nguoi_dung_id;
                    break;
                
                case 'binh_luan':
                    if (!empty($comments)) {
                        $comment = $comments[array_rand($comments)];
                        $binhLuanId = $comment->id;
                        $baiVietId = $comment->bai_viet_id;
                        $nguoiDungId = $comment->nguoi_dung_id;
                    } else {
                        $post = $posts[array_rand($posts)];
                        $baiVietId = $post->id;
                        $nguoiDungId = $post->nguoi_dung_id;
                    }
                    break;

                case 'theo_doi':
                    $nguoiDungId = $users[array_rand($users)];
                    break;
            }

            // Tránh tự gửi thông báo cho chính mình (tùy chọn)
            if ($nguoiDungId == $nguoiThucHienId) {
                continue;
            }

            if ($nguoiDungId) {
                $notifications[] = [
                    'nguoi_dung_id' => $nguoiDungId,
                    'nguoi_thuc_hien_id' => $nguoiThucHienId,
                    'loai' => $loai,
                    'bai_viet_id' => $baiVietId,
                    'binh_luan_id' => $binhLuanId,
                    'da_doc' => (bool)rand(0, 1),
                    'ngay_tao' => Carbon::now()->subMinutes(rand(1, 10000)),
                ];
            }
        }

        // Chèn theo đợt để tối ưu
        foreach (array_chunk($notifications, 50) as $chunk) {
            DB::table('thong_bao')->insert($chunk);
        }

        $this->command->info('✅ ThongBaoSeeder: Đã tạo ' . count($notifications) . ' thông báo.');
    }
}   
