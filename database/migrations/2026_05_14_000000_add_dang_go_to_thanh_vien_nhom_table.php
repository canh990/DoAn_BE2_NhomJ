<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thanh_vien_nhom', function (Blueprint $table) {
            if (!Schema::hasColumn('thanh_vien_nhom', 'dang_go')) {
                $table->boolean('dang_go')->default(false)->after('tat_thong_bao');
            }
        });
    }

    public function down(): void
    {
        Schema::table('thanh_vien_nhom', function (Blueprint $table) {
            if (Schema::hasColumn('thanh_vien_nhom', 'dang_go')) {
                $table->dropColumn('dang_go');
            }
        });
    }
};
