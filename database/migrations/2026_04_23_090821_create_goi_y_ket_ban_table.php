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
       Schema::create('goi_y_ket_ban', function (Blueprint $table) {
    $table->id();
    $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->foreignId('nguoi_goi_y_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->decimal('diem_so', 5, 2)->default(0.00); // điểm gợi ý (bạn chung, vị trí)
    $table->timestamp('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();

    $table->unique(['nguoi_dung_id', 'nguoi_goi_y_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goi_y_ket_ban');
    }
};
