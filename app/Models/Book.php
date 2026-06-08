<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory, SoftDeletes; // Подключаем трейты

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'price',
        'stock'
    ];

    /**
     * Получить все логи движения этой книги на складе (Один-ко-многим)
     */
    public function logs(): HasMany
    {
        return $this->hasMany(BookStockLog::class);
    }

    /**
     * Получить все заказы, в которых участвует эта книга (Многие-ко-многим)
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'price') // Разрешаем доступ к полям из чека
            ->withTimestamps();
    }
}
