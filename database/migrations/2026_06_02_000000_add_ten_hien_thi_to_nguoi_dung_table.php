<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột ten_hien_thi (tên hiển thị) vào bảng nguoi_dung.
     * Khác với ten_dang_nhap (username dùng cho URL), ten_hien_thi là
     * tên thân thiện hiển thị trên UI (tên thật, tiếng Việt, v.v.)
     */
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('ten_hien_thi', 100)->nullable()->after('ten_dang_nhap');
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('ten_hien_thi');
        });
    }
};
