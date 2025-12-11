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
        // Use raw SQL to avoid requiring doctrine/dbal
        DB::statement("ALTER TABLE `users` MODIFY `password` VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Be cautious: making NOT NULL again may fail if NULL values exist.
        DB::statement("ALTER TABLE `users` MODIFY `password` VARCHAR(255) NOT NULL");
    }
};
