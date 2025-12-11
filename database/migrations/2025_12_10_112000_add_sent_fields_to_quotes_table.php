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
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'was_sent')) {
                $table->boolean('was_sent')->default(false)->after('is_approved');
            }
            if (!Schema::hasColumn('quotes', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('was_sent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (Schema::hasColumn('quotes', 'sent_at')) {
                $table->dropColumn('sent_at');
            }
            if (Schema::hasColumn('quotes', 'was_sent')) {
                $table->dropColumn('was_sent');
            }
        });
    }
};
