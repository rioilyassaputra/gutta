<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'sku',
        'stock',
        'additional_price',
    ];

    protected $casts = [
        'stock' => 'integer',
        'additional_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function getFinalPriceAttribute(): float
    {
        return (float) $this->product->price + (float) $this->additional_price;
    }

    public function getDetailAttribute(): string
    {
        return $this->color
            ? "{$this->size} / {$this->color}"
            : $this->size;
    }
}
