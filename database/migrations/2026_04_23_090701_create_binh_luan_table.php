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
       Schema::create('binh_luan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bai_viet_id')->constrained('bai_viet')->cascadeOnDelete();
    $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->foreignId('binh_luan_cha_id')->nullable()->constrained('binh_luan')->cascadeOnDelete();
    $table->text('noi_dung');
    $table->boolean('da_xoa')->default(false);
    $table->timestamp('ngay_tao')->useCurrent();
    $table->timestamp('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();

    $table->index('bai_viet_id');
    $table->index('binh_luan_cha_id');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binh_luan');
    }
};
