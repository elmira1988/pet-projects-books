<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'status'
    ];

    /**
     * Кто сделал этот заказ (Обратная связь Один-ко-многим)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Какие книги куплены в этом заказе (Многие-ко-многим)
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
