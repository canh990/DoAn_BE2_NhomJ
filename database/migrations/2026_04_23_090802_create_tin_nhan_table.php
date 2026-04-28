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
        Schema::create('tin_nhan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cuoc_tro_chuyen_id')->constrained('cuoc_tro_chuyen')->cascadeOnDelete();
    $table->foreignId('nguoi_gui_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->text('noi_dung')->nullable();
    $table->string('trang_thai', 20)->default('da_gui'); // 'da_gui','da_nhan','da_xem'
    $table->boolean('da_thu_hoi')->default(false);
    $table->timestamp('ngay_tao')->useCurrent();

    $table->index(['cuoc_tro_chuyen_id', 'ngay_tao']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tin_nhan');
    }
};
