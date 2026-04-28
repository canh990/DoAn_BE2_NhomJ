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
       Schema::create('cuoc_tro_chuyen', function (Blueprint $table) {
    $table->id();
    $table->string('loai', 20)->default('ca_nhan'); // 'ca_nhan', 'nhom'
    $table->string('ten_nhom', 100)->nullable();
    $table->string('anh_nhom', 500)->nullable();
    $table->timestamp('ngay_tao')->useCurrent();
    $table->timestamp('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuoc_tro_chuyen');
    }
};
