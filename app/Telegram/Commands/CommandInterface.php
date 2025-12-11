<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Api;

interface CommandInterface
{
    /**
     * Handle incoming message/update
     * @param Api $telegram Telegram API client
     * @param mixed $message Message object from SDK
     * @param array $updateArray Raw update as array
     * @return mixed Optional return value (e.g., created models)
     */
    public function handle(Api $telegram, $message, array $updateArray = []);
}
