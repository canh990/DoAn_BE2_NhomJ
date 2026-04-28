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
    Schema::create('dat_lai_mat_khau', function (Blueprint $table) {
        $table->id();
        $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->string('ma_otp', 10);
        $table->timestamp('het_han');
        $table->boolean('da_su_dung')->default(false);
        $table->timestamp('ngay_tao')->useCurrent();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dat_lai_mat_khau');
    }
};
