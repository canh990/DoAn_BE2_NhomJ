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
        Schema::create('tro_giup', function (Blueprint $table) {
            $table->id();
            $table->string('loai'); // 'faq' hoặc 'info'
            $table->string('khoa')->nullable(); // Ví dụ: 'email', 'hotline', etc.
            $table->text('cau_hoi')->nullable(); // Dành cho câu hỏi FAQ
            $table->text('tra_loi')->nullable(); // Câu trả lời FAQ hoặc giá trị cấu hình
            $table->string('ngon_ngu')->default('all'); // 'vi', 'en', 'all'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tro_giup');
    }
};
