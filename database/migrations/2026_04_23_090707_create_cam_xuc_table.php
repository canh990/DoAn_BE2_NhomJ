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
       Schema::create('cam_xuc', function (Blueprint $table) {
    $table->id();
    $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->foreignId('bai_viet_id')->nullable()->constrained('bai_viet')->cascadeOnDelete();
    $table->foreignId('binh_luan_id')->nullable()->constrained('binh_luan')->cascadeOnDelete();
    $table->string('loai_cam_xuc', 20); // 'thich','tim','haha','buon','phan_no','wow'
    $table->timestamp('ngay_tao')->useCurrent();

    $table->unique(['nguoi_dung_id', 'bai_viet_id']);
    $table->unique(['nguoi_dung_id', 'binh_luan_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cam_xuc');
    }
};
