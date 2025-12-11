<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to avoid requiring doctrine/dbal
        DB::statement("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Be cautious: making NOT NULL again may fail if NULL values exist.
        // If you really need to revert, ensure there are no NULL emails first.
        DB::statement("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NOT NULL");
    }
};
