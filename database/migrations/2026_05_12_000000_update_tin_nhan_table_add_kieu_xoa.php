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
        Schema::table('tin_nhan', function (Blueprint $table) {
            $table->string('kieu_xoa', 20)->nullable()->default(null)->comment('ca_nhan: delete for me, ca_hai: unsend for everyone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tin_nhan', function (Blueprint $table) {
            $table->dropColumn('kieu_xoa');
        });
    }
};
