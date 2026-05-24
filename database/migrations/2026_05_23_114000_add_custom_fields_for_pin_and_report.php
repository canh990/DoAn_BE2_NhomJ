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
        Schema::table('binh_luan', function (Blueprint $table) {
            if (!Schema::hasColumn('binh_luan', 'da_ghim')) {
                $table->boolean('da_ghim')->default(false)->after('da_xoa');
            }
        });

        Schema::table('thong_bao', function (Blueprint $table) {
            if (!Schema::hasColumn('thong_bao', 'noi_dung')) {
                $table->text('noi_dung')->nullable()->after('da_doc');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('binh_luan', function (Blueprint $table) {
            if (Schema::hasColumn('binh_luan', 'da_ghim')) {
                $table->dropColumn('da_ghim');
            }
        });

        Schema::table('thong_bao', function (Blueprint $table) {
            if (Schema::hasColumn('thong_bao', 'noi_dung')) {
                $table->dropColumn('noi_dung');
            }
        });
    }
};
