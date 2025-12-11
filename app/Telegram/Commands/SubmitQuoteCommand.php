<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Quote;

class SubmitQuoteCommand implements CommandInterface
{
    public function handle(Api $telegram, $message, array $updateArray = [])
    {
        $chatId = $message->getChat()->getId();
        $text = $message->getText() ?? '';

        if (trim($text) === '') {
            $text = '(цитата без текста)';
        }

        try {
            // Создаём временно цитату без имени
            $user = User::firstOrCreate(['telegram_id' => $chatId], ['name' => null]);
            $quote = Quote::create([
                'user_id' => $user->id,
                'sender_name' => null,
                'quote_text' => $text,
                'is_approved' => false,
            ]);

            // Инлайн-кнопки: анонимно / под именем
            $replyMarkup = json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Отправить анонимно', 'callback_data' => 'anonymous:' . $quote->id],
                        ['text' => 'Отправить под своим именем', 'callback_data' => 'with_name:' . $quote->id]
                    ]
                ]
            ]);

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Теперь введи, пожалуйста, своё имя или псевдоним:',
                'reply_markup' => $replyMarkup
            ]);

            return $quote;
        } catch (\Throwable $e) {
            Log::error('SubmitQuoteCommand failed', ['error' => $e->getMessage(), 'chat_id' => $chatId]);
            try {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Произошла ошибка при сохранении цитаты. Попробуй позже.'
                ]);
            } catch (\Throwable $ex) {
                Log::error('Telegram sendMessage failed (error reply)', ['error' => $ex->getMessage()]);
            }
            return null;
        }
    }
}