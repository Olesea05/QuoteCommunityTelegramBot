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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            // Пользователь, который добавил в избранное
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Цитата, которую добавили в избранное
            $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade');

            $table->timestamps();

            // Чтобы один пользователь не добавлял одну и ту же цитату дважды
            $table->unique(['user_id', 'quote_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};