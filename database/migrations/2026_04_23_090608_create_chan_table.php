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
    Schema::create('chan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('nguoi_chan_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->foreignId('nguoi_bi_chan_id')->constrained('nguoi_dung')->cascadeOnDelete();
        $table->timestamp('ngay_tao')->useCurrent();

        $table->unique(['nguoi_chan_id', 'nguoi_bi_chan_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chan');
    }
};
