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
       Schema::create('media_binh_luan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('binh_luan_id')->constrained('binh_luan')->cascadeOnDelete();
    $table->string('loai', 20);         // 'hinh_anh', 'gif', 'sticker'
    $table->string('duong_dan', 500);
    $table->timestamp('ngay_tao')->useCurrent();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_binh_luan');
    }
};
