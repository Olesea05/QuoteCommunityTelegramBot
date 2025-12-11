<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Добавить цитату в избранное
     */
    public function addFavorite(Request $request)
    {
        $favorite = Favorite::create([
            'user_id' => $request->user_id,
            'quote_id' => $request->quote_id,
        ]);

        return response()->json(['success' => true, 'favorite_id' => $favorite->id]);
    }

    /**
     * Удалить цитату из избранного
     */
    public function removeFavorite(Request $request)
    {
        Favorite::where('user_id', $request->user_id)
                ->where('quote_id', $request->quote_id)
                ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Получить все избранные цитаты пользователя
     */
    public function listFavorites($userId)
    {
        $favorites = Favorite::with('quote')
                             ->where('user_id', $userId)
                             ->get();

        return response()->json($favorites);
    }
}