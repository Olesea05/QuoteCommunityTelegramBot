<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;
use App\Models\Quote;

class QuoteCommand implements CommandInterface
{
    public function handle(Api $telegram, $message, array $updateArray = []): void
    {
        $chatId = $message->getChat()->getId();

        try {
            $quote = Quote::where('is_approved', true)->inRandomOrder()->first();

            if (!$quote) {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Пока цитат нет. Ты можешь предложить свою командой /new.'
                ]);
                return;
            }

            $textParts = [];
            if ($quote->quote_text) {
                $textParts[] = "«{$quote->quote_text}»";
            }
            if ($quote->author) {
                $textParts[] = '- ' . $quote->author;
            }
            if ($quote->source_type && $quote->source_title) {
                $textParts[] = "Источник ({$quote->source_type}): {$quote->source_title}";
            }

            $sendText = implode("\n", $textParts);

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $sendText
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage failed (/quote)', ['error' => $e->getMessage(), 'chat_id' => $chatId]);
        }
    }
}
