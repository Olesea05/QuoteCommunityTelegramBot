<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    /**
     * Пользователь предлагает новую цитату
     */
    public function submitQuote(Request $request)
    {
        $quote = Quote::create([
            'user_id' => $request->user_id,
            'sender_name' => $request->sender_name,
            'quote_text' => $request->quote_text,
            'source_type' => $request->source_type,
            'source_title' => $request->source_title,
            'author' => $request->author,
            'is_approved' => false,
        ]);

        return response()->json(['success' => true, 'quote_id' => $quote->id]);
    }

    /**
     * Админ одобряет цитату
     */
    public function approveQuote($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->is_approved = true;
        $quote->save();

        return response()->json(['success' => true]);
    }

    /**
     * Список всех одобренных цитат
     */
    public function listApprovedQuotes()
    {
        $quotes = Quote::where('is_approved', true)->get();
        return response()->json($quotes);
    }
}