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
        Schema::create('bai_viet_hashtag', function (Blueprint $table) {
    $table->foreignId('bai_viet_id')->constrained('bai_viet')->cascadeOnDelete();
    $table->foreignId('hashtag_id')->constrained('hashtag')->cascadeOnDelete();

    $table->primary(['bai_viet_id', 'hashtag_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_viet_hashtag');
    }
};
