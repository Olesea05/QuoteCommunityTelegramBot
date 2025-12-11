<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;

class StartCommand implements CommandInterface
{
    public function handle(Api $telegram, $message, array $updateArray = []): void
    {
        $chatId = $message->getChat()->getId();

        $keyboard = [
            'keyboard' => [
                [
                    ['text' => '/new'],
                    ['text' => '/start'],
                    ['text' => '/help']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];

        try {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Привет 😄\nЯ бот сообщества любителей цитат ✨\n\nКаждый день я буду присылать тебе случайную цитату от наших участников 📚\nТы тоже можешь предложить свою цитату и поделиться мудростью с другими ♥️\n\nЧтобы узнать, что я умею, нажми /help",
                'reply_markup' => json_encode($keyboard)
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage failed (/start)', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId
            ]);
        }
    }
}