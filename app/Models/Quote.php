<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово заполнять.
     */
    protected $fillable = [
        'user_id',
        'sender_name',
        'quote_text',
        'source_type',
        'source_title',
        'author',
        'is_approved',
    ];

    /**
     * Связь: цитата принадлежит пользователю.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь: цитата может быть добавлена в избранное.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}