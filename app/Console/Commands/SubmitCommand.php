<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\QuoteController;

class SubmitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quote:submit {chatId} {text*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправить новую цитату на модерацию (используется вручную)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chatId = $this->argument('chatId');
        $textParts = $this->argument('text');
        $text = is_array($textParts) ? implode(' ', $textParts) : $textParts;

        // Передаём данные в контроллер
        $controller = new QuoteController();
        $controller->submitQuote($chatId, $text);

        $this->info('Спасибо! Твоя цитата отправлена на модерацию.');

        return 0;
    }
}