<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('quyen_rieng_tu', 20)->default('public')->change();
        });

        // Convert existing data
        DB::table('nguoi_dung')->where('quyen_rieng_tu', 'cong_khai')->update(['quyen_rieng_tu' => 'public']);
        DB::table('nguoi_dung')->where('quyen_rieng_tu', 'rieng_tu')->update(['quyen_rieng_tu' => 'private']);
        DB::table('nguoi_dung')->where('quyen_rieng_tu', 'ban_be')->update(['quyen_rieng_tu' => 'private']); // Map friends to private for simplicity as requested
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('quyen_rieng_tu', 20)->default('cong_khai')->change();
        });
        
        DB::table('nguoi_dung')->where('quyen_rieng_tu', 'public')->update(['quyen_rieng_tu' => 'cong_khai']);
        DB::table('nguoi_dung')->where('quyen_rieng_tu', 'private')->update(['quyen_rieng_tu' => 'rieng_tu']);
    }
};
