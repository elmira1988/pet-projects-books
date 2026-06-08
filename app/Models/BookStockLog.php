<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookStockLog extends Model
{
    // Отключаем автоматические поля updated_at, так как логи только создаются
    public $timestamps = false;

    protected $fillable = [
        'book_id',
        'user_id',
        'type',
        'quantity',
        'reason'
    ];

    // Указываем Laravel автоматически заполнять поле created_at при создании строки
    protected static function booted()
    {
        static::creating(function ($log) {
            $log->created_at = now();
        });
    }

    /**
     * К какой книге относится этот лог
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Какой пользователь совершил это действие
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
