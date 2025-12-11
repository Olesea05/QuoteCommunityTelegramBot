<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;

class HelpCommand implements CommandInterface
{
    public function handle(Api $telegram, $message, array $updateArray = []): void
    {
        $chatId = $message->getChat()->getId();

        $text = "Доступные команды:\n"
              . "/new — отправить новую цитату\n"
              . "/start — показать приветствие\n"
              . "/help — показать список команд\n";

        try {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage failed (/help)', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId
            ]);
        }
    }
}