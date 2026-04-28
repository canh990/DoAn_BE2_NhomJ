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
      Schema::create('bao_cao', function (Blueprint $table) {
    $table->id();
    $table->foreignId('nguoi_bao_cao_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->foreignId('bai_viet_id')->nullable()->constrained('bai_viet')->nullOnDelete();
    $table->foreignId('binh_luan_id')->nullable()->constrained('binh_luan')->nullOnDelete();
    $table->foreignId('nguoi_dung_bi_bao_cao_id')->nullable()->constrained('nguoi_dung')->nullOnDelete();
    $table->string('ly_do', 500);
    $table->string('trang_thai', 20)->default('cho_xu_ly'); // 'cho_xu_ly','da_xu_ly','bo_qua'
    $table->timestamp('ngay_tao')->useCurrent();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bao_cao');
    }
};
