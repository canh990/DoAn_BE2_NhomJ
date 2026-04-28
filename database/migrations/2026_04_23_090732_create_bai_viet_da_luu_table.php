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
       Schema::create('bai_viet_da_luu', function (Blueprint $table) {
    $table->id();
    $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
    $table->foreignId('bai_viet_id')->constrained('bai_viet')->cascadeOnDelete();
    $table->timestamp('ngay_tao')->useCurrent();

    $table->unique(['nguoi_dung_id', 'bai_viet_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_viet_da_luu');
    }
};
