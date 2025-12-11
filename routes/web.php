<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;

Route::get('/', function () {
    return view('welcome');
});

// Telegram webhook routes
Route::post('/webhook', [TelegramController::class, 'handle']);
// Also accept the /api/webhook path in case the webhook is set with an /api prefix
// Attach the 'api' middleware group so CSRF/session middleware is not applied.
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::post('/api/webhook', [TelegramController::class, 'handle'])
    ->middleware('api')
    ->withoutMiddleware([VerifyCsrfToken::class]);
