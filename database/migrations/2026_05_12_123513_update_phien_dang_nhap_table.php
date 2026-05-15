<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //

        Schema::table('phien_dang_nhap', function (Blueprint $table) {

            // tên thiết bị
            $table->string('ten_thiet_bi')
                ->nullable()
                ->after('thong_tin_thiet_bi');

            // trình duyệt
            $table->string('trinh_duyet')
                ->nullable()
                ->after('ten_thiet_bi');

            // hệ điều hành
            $table->string('he_dieu_hanh')
                ->nullable()
                ->after('trinh_duyet');

            // user agent đầy đủ
            $table->text('user_agent')
                ->nullable()
                ->after('he_dieu_hanh');

            // hoạt động cuối
            $table->timestamp('lan_hoat_dong_cuoi')
                ->nullable()
                ->after('dia_chi_ip');

            // đã đăng xuất chưa
            $table->timestamp('dang_xuat_luc')
                ->nullable()
                ->after('lan_hoat_dong_cuoi');

            // phiên hiện tại
            $table->boolean('la_phien_hien_tai')
                ->default(false)
                ->after('dang_xuat_luc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('phien_dang_nhap', function (Blueprint $table) {

            $table->dropColumn([
                'ten_thiet_bi',
                'trinh_duyet',
                'he_dieu_hanh',
                'user_agent',
                'lan_hoat_dong_cuoi',
                'dang_xuat_luc',
                'la_phien_hien_tai',
            ]);
        });
    }
};
