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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            // Привязка к таблице users (может быть null, если анонимно)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Имя отправителя (если хочет указать своё)
            $table->string('sender_name')->nullable();

            // Основной текст цитаты
            $table->text('quote_text');

            // Тип источника: book / song / personal
            $table->enum('source_type', ['book', 'song', 'personal']);

            // Название книги/песни (не требуется, если personal)
            $table->string('source_title')->nullable();

            // Автор цитаты или автор книги/песни
            $table->string('author')->nullable();

            // Одобрена ли цитата админом
            $table->boolean('is_approved')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};