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
    Schema::create('nguoi_dung', function (Blueprint $table) {
        $table->id();
        $table->string('ten_dang_nhap', 50)->unique();
        $table->string('email', 191)->unique()->nullable();
        $table->string('so_dien_thoai', 20)->unique()->nullable();
        $table->string('mat_khau_hash', 255)->nullable();
        $table->string('anh_dai_dien', 500)->nullable();
        $table->string('anh_bia', 500)->nullable();
        $table->text('tieu_su')->nullable();
        $table->date('ngay_sinh')->nullable();
        $table->string('noi_o', 255)->nullable();
        $table->string('quyen_rieng_tu', 20)->default('cong_khai');
        $table->boolean('da_xac_thuc')->default(false);
        $table->boolean('con_hoat_dong')->default(true);
        $table->string('nha_cung_cap_oauth', 50)->nullable();
        $table->string('id_oauth', 255)->nullable();
        $table->timestamps();
        $table->softDeletes('ngay_xoa');

        $table->index('email');
        $table->index('ten_dang_nhap');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
