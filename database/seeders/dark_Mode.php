<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\PostImgSeeders;
use Database\Seeders\PostSeeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (!User::where('ten_dang_nhap', 'test_user')->exists()) {
            User::create([
                'ten_dang_nhap' => 'test_user',
                'email' => 'test@example.com',
                'mat_khau_hash' => Hash::make('password123'),
                'quyen_rieng_tu' => 'cong_khai',
                'da_xac_thuc' => true,
            ]);
        }

        $this->call(BinhLuanSeeder::class);
        $this->call(Camxucseeder::class);
        $this->call(NguoiDungSeeder::class);
        $this->call(PostSeeders::class);
        $this->call(PostImgSeeders::class);
        $this->call(SharesSeeders::class);
        $this->call(MediaBinhLuanSeeders::class);
        $this->call(ReplySeeders::class);
        $this->call(ThongBaoSeeder::class);
    }
}
