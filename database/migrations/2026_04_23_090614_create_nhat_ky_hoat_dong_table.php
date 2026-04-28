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
    Schema::create('nhat_ky_hoat_dong', function (Blueprint $table) {
        $table->id();
        $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->string('hanh_dong', 50);
        $table->unsignedInteger('doi_tuong_id');
        $table->string('loai_doi_tuong', 30);
        $table->timestamp('ngay_tao')->useCurrent();

        $table->index('nguoi_dung_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhat_ky_hoat_dong');
    }
};
