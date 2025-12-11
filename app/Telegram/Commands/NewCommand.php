<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;

class NewCommand implements CommandInterface
{
    public function handle(Api $telegram, $message, array $updateArray = []): void
    {
        $chatId = $message->getChat()->getId();

        try {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Отправь, пожалуйста, цитату, которую хочешь предложить (можешь отправить также автора и/или источник):'
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage failed (/new)', ['error' => $e->getMessage(), 'chat_id' => $chatId]);
        }
    }
}
