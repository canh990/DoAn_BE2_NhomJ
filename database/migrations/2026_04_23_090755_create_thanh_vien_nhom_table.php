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
        Schema::create('thanh_vien_nhom', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cuoc_tro_chuyen_id')->constrained('cuoc_tro_chuyen')->cascadeOnDelete();
    $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->string('vai_tro', 20)->default('thanh_vien'); // 'quan_tri', 'thanh_vien'
    $table->boolean('tat_thong_bao')->default(false);     // mute
    $table->timestamp('ngay_tham_gia')->useCurrent();
    $table->timestamp('doc_den_luc')->nullable();          // read receipts

    $table->unique(['cuoc_tro_chuyen_id', 'nguoi_dung_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_vien_nhom');
    }
};
