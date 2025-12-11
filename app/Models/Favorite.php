<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово заполнять.
     */
    protected $fillable = [
        'user_id',
        'quote_id',
    ];

    /**
     * Связь: избранная цитата принадлежит пользователю.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь: избранная цитата принадлежит цитате.
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}