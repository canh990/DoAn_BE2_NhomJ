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
       Schema::create('tin_24h', function (Blueprint $table) {
    $table->id();
    $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->string('duong_dan_media', 500);
    $table->string('loai_media', 20);   // 'hinh_anh', 'video'
    $table->timestamp('het_han');       // tự xóa sau 24h
    $table->timestamp('ngay_tao')->useCurrent();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tin_24h');
    }
};
