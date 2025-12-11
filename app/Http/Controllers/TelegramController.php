<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Quote;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\NewCommand;
use App\Telegram\Commands\SubmitQuoteCommand;
use App\Telegram\Commands\HelpCommand;

class TelegramController extends Controller
{
    public function handle()
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $update = $telegram->getWebhookUpdates();

        $message = $update->getMessage();
        $callback = $update->getCallbackQuery();

        // --- Callback кнопки ---
        if ($callback) {
            $this->handleCallbackQuery($telegram, $callback);
            return response('ok');
        }

        if (!$message) return response('ok');

        $chatId = $message->getChat()->getId();
        $text = $message->getText() ?? '';

        // --- Если пользователь находится в режиме редактирования цитаты админом ---
        $editKey = "telegram_quote_edit_{$chatId}";
        if (Cache::has($editKey)) {
            $quoteId = Cache::get($editKey);
            $quote = Quote::find($quoteId);
            if ($quote) {
                $oldText = $quote->quote_text;
                $quote->quote_text = $text;
                $quote->save();
                Cache::forget($editKey);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Цитата обновлена.\nСтарый текст:\n{$oldText}\nНовый текст:\n{$quote->quote_text}"
                ]);

                $this->notifyAdminsAboutQuote($telegram, $quote);
            }
            return response('ok');
        }

        // --- Если пользователь вводит своё имя/псевдоним ---
        $nameKey = "telegram_quote_sender_{$chatId}";
        if (Cache::has($nameKey)) {
            $quoteId = Cache::get($nameKey);
            $quote = Quote::find($quoteId);
            if ($quote) {
                $quote->sender_name = $text ?: '(аноним)';
                $quote->save();
                Cache::forget($nameKey);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Спасибо! Твоя цитата отправлена на модерацию."
                ]);

                $this->notifyAdminsAboutQuote($telegram, $quote);
            }
            return response('ok');
        }

        // --- Обработка команд через слэш или нажатие кнопки клавиатуры ---
        $commandMap = [
            '/start' => StartCommand::class,
            '/new'   => NewCommand::class,
            '/help'  => HelpCommand::class,
        ];

        if (isset($commandMap[$text])) {
            try {
                $handler = new $commandMap[$text]();
                $handler->handle($telegram, $message);
            } catch (\Throwable $e) {
                Log::error('Command handler failed', [
                    'command' => $text,
                    'error' => $e->getMessage()
                ]);
            }
            return response('ok');
        }

        // --- Новый текст как цитата ---
        if (!preg_match('/^\//', trim($text))) {
            try {
                $submitHandler = new SubmitQuoteCommand();
                $submitHandler->handle($telegram, $message);
            } catch (\Throwable $e) {
                Log::error('SubmitQuote handler failed', ['error' => $e->getMessage()]);
            }
        }

        return response('ok');
    }

    protected function handleCallbackQuery(Api $telegram, $callback): void
    {
        try {
            $callbackId = $callback->getId();
            $from = $callback->getFrom();
            $chatId = $from->getId();
            $messageId = $callback->getMessage()->getMessageId();
            $data = $callback->getData();

            if (!$data) return;

            [$action, $id] = array_pad(explode(':', $data, 2), 2, null);
            $id = intval($id);

            $quote = Quote::find($id);
            if (!$quote) return;

            // --- Кнопки пользователя ---
            if ($action === 'anonymous') {
                $quote->sender_name = null;
                $quote->save();

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Спасибо! Твоя цитата отправлена анонимно на модерацию."
                ]);

                $this->notifyAdminsAboutQuote($telegram, $quote);
                return;
            }

            if ($action === 'with_name') {
                Cache::put("telegram_quote_sender_{$chatId}", $quote->id, 3600);
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Отправь, пожалуйста, своё имя или псевдоним."
                ]);
                return;
            }

            // --- Кнопки админа ---
            $admins = array_filter(array_map('trim', explode(',', env('ADMIN_TELEGRAM_IDS', ''))));
            if (!in_array((string)$chatId, $admins, true)) return;

            if ($action === 'edit') {
                Cache::put("telegram_quote_edit_{$chatId}", $quote->id, 3600);
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Введи новый текст цитаты (ID: {$quote->id})."
                ]);
                return;
            }

            if ($action === 'approve') {
                $quote->is_approved = true;
                $quote->save();

                $telegram->answerCallbackQuery([
                    'callback_query_id' => $callbackId,
                    'text' => "Цитата #{$quote->id} одобрена."
                ]);

                try {
                    if ($quote->user && $quote->user->telegram_id) {
                        $telegram->sendMessage([
                            'chat_id' => $quote->user->telegram_id,
                            'text' => "Ваша цитата (ID: {$quote->id}) была одобрена."
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to notify user about approval', ['error' => $e->getMessage()]);
                }

                return;
            }

            if ($action === 'reject') {
                $quote->delete();
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $callbackId,
                    'text' => "Цитата #{$id} отклонена."
                ]);
                return;
            }

        } catch (\Throwable $e) {
            Log::error('handleCallbackQuery failed', ['error' => $e->getMessage()]);
        }
    }

    protected function notifyAdminsAboutQuote(Api $telegram, Quote $quote): void
    {
        $adminList = env('ADMIN_TELEGRAM_IDS', '');
        if (empty($adminList)) return;

        $admins = array_filter(array_map('trim', explode(',', $adminList)));
        $text = "Новая цитата (ID: {$quote->id})\n\n";
        $text .= "Текст: {$quote->quote_text}\n";
        $text .= "Отправил: " . ($quote->sender_name ?? '(аноним)') . "\n";
        $text .= "Дата: {$quote->created_at->toDateTimeString()}\n\n";

        $replyMarkup = json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '✏️ Редактировать', 'callback_data' => "edit:{$quote->id}"],
                    ['text' => '✅ Одобрить', 'callback_data' => "approve:{$quote->id}"],
                    ['text' => '🗑 Отклонить', 'callback_data' => "reject:{$quote->id}"]
                ]
            ]
        ]);

        foreach ($admins as $adminId) {
            try {
                $telegram->sendMessage([
                    'chat_id' => $adminId,
                    'text' => $text,
                    'reply_markup' => $replyMarkup
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to notify admin', [
                    'admin' => $adminId,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}