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
       Schema::create('binh_chon', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bai_viet_id')->unique()->constrained('bai_viet')->cascadeOnDelete();
    $table->string('cau_hoi', 500);
    $table->timestamp('ngay_ket_thuc')->nullable();
    $table->timestamp('ngay_tao')->useCurrent();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binh_chon');
    }
};
