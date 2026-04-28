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
    Schema::create('theo_doi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('nguoi_theo_doi_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->foreignId('nguoi_duoc_theo_doi_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->string('trang_thai', 20)->default('da_chap_nhan');
        $table->timestamp('ngay_tao')->useCurrent();

        $table->unique(['nguoi_theo_doi_id', 'nguoi_duoc_theo_doi_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theo_doi');
    }
};
