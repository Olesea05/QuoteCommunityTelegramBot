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
        // Make column additions idempotent to avoid duplicate column errors
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'telegram_id')) {
                    $table->bigInteger('telegram_id')->unique()->nullable()->after('id');
                }
                if (!Schema::hasColumn('users', 'name')) {
                    $table->string('name')->nullable()->after('id');
                }
            });
        }

        if (Schema::hasTable('quotes')) {
            Schema::table('quotes', function (Blueprint $table) {
                if (!Schema::hasColumn('quotes', 'sender_name')) {
                    $table->string('sender_name')->nullable();
                }
            });
        }

        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                if (!Schema::hasColumn('jobs', 'name')) {
                    $table->string('name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns automatically in rollback to avoid data loss.
    }
};
