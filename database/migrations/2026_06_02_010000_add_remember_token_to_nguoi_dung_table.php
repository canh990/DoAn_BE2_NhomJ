<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột remember_token để hỗ trợ chức năng "Ghi nhớ đăng nhập" của Laravel.
     * Laravel sẽ tự động set cookie remember_me 30 ngày khi Auth::attempt(..., true).
     */
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->rememberToken()->after('id_oauth');
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
