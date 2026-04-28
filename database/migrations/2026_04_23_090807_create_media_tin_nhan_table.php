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
       Schema::create('media_tin_nhan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tin_nhan_id')->constrained('tin_nhan')->cascadeOnDelete();
    $table->string('loai', 20); // 'hinh_anh','video','am_thanh','tap_tin'
    $table->string('duong_dan', 500);
    $table->timestamp('ngay_tao')->useCurrent();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_tin_nhan');
    }
};
