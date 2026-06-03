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
        Schema::table('nguoi_dung', function (Blueprint $table) {
            if (!Schema::hasColumn('nguoi_dung', 'role')) {
                $table->string('role', 20)->default('user')->after('con_hoat_dong');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            if (Schema::hasColumn('nguoi_dung', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
