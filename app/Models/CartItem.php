<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_variant_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id')
            ->with(['product.images']);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->variant->getFinalPriceAttribute() * $this->qty;
    }
}
