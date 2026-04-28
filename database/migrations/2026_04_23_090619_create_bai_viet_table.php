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
    Schema::create('bai_viet', function (Blueprint $table) {
        $table->id();
        $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->foreignId('bai_goc_id')->nullable()->constrained('bai_viet')->nullOnDelete();
        $table->string('loai', 30)->default('van_ban');
        $table->text('noi_dung')->nullable();
        $table->string('ten_dia_diem', 255)->nullable();
        $table->decimal('vi_do', 10, 8)->nullable();
        $table->decimal('kinh_do', 11, 8)->nullable();
        $table->string('cam_xuc', 100)->nullable();
        $table->string('hoat_dong', 100)->nullable();
        $table->string('quyen_rieng_tu', 20)->default('cong_khai');
        $table->boolean('da_ghim')->default(false);
        $table->boolean('da_chinh_sua')->default(false);
        $table->boolean('da_xoa')->default(false);
        $table->timestamps();

        $table->index('nguoi_dung_id');
        $table->index('created_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_viet');
    }
};
