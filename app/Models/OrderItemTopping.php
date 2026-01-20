<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\OrderItemTopping
 *
 * @property int $id
 * @property int $order_item_id
 * @property int $topping_id
 * @property float $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OrderItem $orderItem
 * @property-read \App\Models\Topping $topping
 */
class OrderItemTopping extends Pivot
{
    // Using Pivot class instead of Model because it's an intermediate table
    // with extra attributes (price). 
    // Laravel 10 supports Pivot models with ID (using $incrementing = true).

    protected $table = 'order_item_toppings';

    public $incrementing = true; // Since we have an 'id' column

    protected $fillable = [
        'order_item_id',
        'topping_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function topping(): BelongsTo
    {
        return $this->belongsTo(Topping::class);
    }
}
