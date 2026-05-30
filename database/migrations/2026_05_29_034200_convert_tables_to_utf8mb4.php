<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $dbName = DB::getDatabaseName();

            // 1. Alter database character set and collation
            DB::statement("ALTER DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2. Retrieve all tables in the current database
            $tables = DB::table('information_schema.tables')
                ->where('table_schema', $dbName)
                ->where('table_type', 'BASE TABLE')
                ->get()
                ->map(function ($row) {
                    $row = (array) $row;
                    $key = collect(array_keys($row))->first(fn ($k) => strtolower($k) === 'table_name');
                    return $row[$key] ?? null;
                })
                ->filter();

            // Fix index length issue for email in nguoi_dung table before conversion
            DB::statement("ALTER TABLE `nguoi_dung` MODIFY `email` VARCHAR(191) NULL");
            DB::statement("ALTER TABLE `phien_dang_nhap` MODIFY `token_hash` VARCHAR(191) NOT NULL");

            // 3. Alter all tables and their columns to utf8mb4
            foreach ($tables as $tableName) {
                if (in_array(strtolower($tableName), ['cache', 'cache_locks', 'failed_jobs', 'jobs', 'job_batches', 'sessions', 'migrations', 'password_resets', 'password_reset_tokens', 'users'])) {
                    continue;
                }
                DB::statement("ALTER TABLE `{$tableName}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $dbName = DB::getDatabaseName();

            // 1. Alter database character set back to utf8
            DB::statement("ALTER DATABASE `{$dbName}` CHARACTER SET utf8 COLLATE utf8_unicode_ci");

            // 2. Retrieve all tables in the current database
            $tables = DB::table('information_schema.tables')
                ->where('table_schema', $dbName)
                ->where('table_type', 'BASE TABLE')
                ->get()
                ->map(function ($row) {
                    $row = (array) $row;
                    $key = collect(array_keys($row))->first(fn ($k) => strtolower($k) === 'table_name');
                    return $row[$key] ?? null;
                })
                ->filter();

            // 3. Alter all tables and their columns back to utf8
            foreach ($tables as $tableName) {
                if (in_array(strtolower($tableName), ['cache', 'cache_locks', 'failed_jobs', 'jobs', 'job_batches', 'sessions', 'migrations', 'password_resets', 'password_reset_tokens', 'users'])) {
                    continue;
                }
                DB::statement("ALTER TABLE `{$tableName}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
            }
        }
    }
};
