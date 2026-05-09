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
        Schema::table('tin_24h', function (Blueprint $table) {
            $table->string('quyen_rieng_tu', 20)->default('cong_khai')->after('loai_media');
        });
    }

    public function down(): void
    {
        Schema::table('tin_24h', function (Blueprint $table) {
            $table->dropColumn('quyen_rieng_tu');
        });
    }
};
