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
        Schema::table('cai_dat_nguoi_dung', function (Blueprint $table) {
            $table->double('dung_luong_cache')->default(10.5); // Khởi tạo một số bộ nhớ đệm ban đầu (e.g. 10.5 MB) làm mẫu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cai_dat_nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('dung_luong_cache');
        });
    }
};
