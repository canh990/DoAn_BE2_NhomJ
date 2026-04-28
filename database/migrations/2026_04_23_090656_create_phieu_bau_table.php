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
        Schema::create('phieu_bau', function (Blueprint $table) {
    $table->id();
    $table->foreignId('binh_chon_id')->constrained('binh_chon')->cascadeOnDelete();
    $table->foreignId('lua_chon_id')->constrained('lua_chon_binh_chon')->cascadeOnDelete();
    $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->timestamp('ngay_tao')->useCurrent();

    $table->unique(['binh_chon_id', 'nguoi_dung_id']); // mỗi người chỉ bầu 1 lần
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_bau');
    }
};
