<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thong_bao', function (Blueprint $table) {
            $table->foreignId('cuoc_tro_chuyen_id')
                ->nullable()
                ->after('binh_luan_id')
                ->constrained('cuoc_tro_chuyen')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thong_bao', function (Blueprint $table) {
            $table->dropForeign(['cuoc_tro_chuyen_id']);
            $table->dropColumn('cuoc_tro_chuyen_id');
        });
    }
};
