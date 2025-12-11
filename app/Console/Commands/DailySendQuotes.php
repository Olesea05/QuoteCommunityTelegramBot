<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;
use App\Models\Quote;
use App\Models\User;

class DailySendQuotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotes:daily-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a random approved quote to all users and mark it as sent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

        // pick a random approved and not-yet-sent quote
        $quote = Quote::where('is_approved', true)
            ->where(function ($q) { $q->whereNull('was_sent')->orWhere('was_sent', false); })
            ->inRandomOrder()
            ->first();

        if (! $quote) {
            $this->info('No approved unsent quotes found. Nothing to send.');
            Log::info('quotes:daily-send — no approved unsent quotes');
            return 0;
        }

        // Build message
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

        // collect recipients
        $recipients = User::whereNotNull('telegram_id')->pluck('telegram_id')->unique();

        foreach ($recipients as $chatId) {
            try {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => $sendText,
                ]);
            } catch (\Throwable $e) {
                Log::error('quotes:daily-send failed to send to user', ['chat_id' => $chatId, 'error' => $e->getMessage()]);
            }
        }

        // mark quote as sent
        try {
            $quote->was_sent = true;
            $quote->sent_at = now();
            $quote->save();
        } catch (\Throwable $e) {
            Log::error('quotes:daily-send failed to mark quote as sent', ['quote_id' => $quote->id, 'error' => $e->getMessage()]);
        }

        $this->info('Quote sent to ' . $recipients->count() . ' users (quote id: ' . $quote->id . ')');
        Log::info('quotes:daily-send completed', ['quote_id' => $quote->id, 'recipients' => $recipients->count()]);

        return 0;
    }
}
