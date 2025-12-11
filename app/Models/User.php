<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Атрибуты, которые можно массово заполнять.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'telegram_id',
    ];

    /**
     * Атрибуты, которые скрываются при сериализации.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Атрибуты с кастингом типов.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Связь: один пользователь может предложить много цитат.
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Связь: один пользователь может иметь много избранных цитат.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}