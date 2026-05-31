<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('phien_dang_nhap', function (Blueprint $table) {
        $table->id();
        $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->string('thong_tin_thiet_bi', 255)->nullable();
        $table->string('dia_chi_ip', 45)->nullable();
        $table->string('token_hash', 191);
        $table->timestamp('het_han');
        $table->timestamp('ngay_tao')->useCurrent();

        $table->index('token_hash');
        $table->index('nguoi_dung_id');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phien_dang_nhap');
    }
};
