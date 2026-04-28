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
      Schema::create('cai_dat_nguoi_dung', function (Blueprint $table) {
    $table->id();
    $table->foreignId('nguoi_dung_id')->unique()->constrained('nguoi_dung')->cascadeOnDelete();
    $table->boolean('che_do_toi')->default(false);
    $table->string('ngon_ngu', 10)->default('vi');
    $table->timestamp('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cai_dat_nguoi_dung');
    }
};
