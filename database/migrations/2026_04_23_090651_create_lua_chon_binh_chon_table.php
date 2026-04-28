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
       Schema::create('lua_chon_binh_chon', function (Blueprint $table) {
    $table->id();
    $table->foreignId('binh_chon_id')->constrained('binh_chon')->cascadeOnDelete();
    $table->string('noi_dung', 255);
    $table->timestamp('ngay_tao')->useCurrent();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lua_chon_binh_chon');
    }
};
