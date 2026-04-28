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
       Schema::create('tag_bai_viet', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bai_viet_id')->constrained('bai_viet')->cascadeOnDelete();
        $table->foreignId('nguoi_dung_duoc_tag_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->timestamp('ngay_tao')->useCurrent();

        $table->unique(['bai_viet_id', 'nguoi_dung_duoc_tag_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_bai_viet');
    }
};
