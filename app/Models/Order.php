<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'address_snapshot',
        'subtotal',
        'shipping_cost',
        'total',
        'courier',
        'courier_service',
        'status',
        'payment_method',
        'payment_token',
        'paid_at',
        'tracking_number',
        'shipped_at',
        'completed_at',
    ];

    protected $casts = [
        'address_snapshot' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
    ];



    // ── Relationships ─────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ── Accessors / Helpers ───────────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'paid'            => 'Dibayar',
            'processing'      => 'Diproses',
            'shipped'         => 'Dikirim',
            'completed'       => 'Selesai',
            'cancelled'       => 'Dibatalkan',
            default           => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'bg-yellow-400 text-black',
            'paid'            => 'bg-blue-500 text-white',
            'processing'      => 'bg-indigo-500 text-white',
            'shipped'         => 'bg-cyan-500 text-white',
            'completed'       => 'bg-green-500 text-white',
            'cancelled'       => 'bg-red-500 text-white',
            default           => 'bg-gray-500 text-white',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getTrackingUrlAttribute(): ?string
    {
        if (! $this->tracking_number) return null;

        return match(strtolower($this->courier)) {
            'jne'     => "https://www.jne.co.id/id/tracking/trace?awb={$this->tracking_number}",
            'jnt'     => "https://www.jet.co.id/track",
            'sicepat' => "https://www.sicepat.com/checkAwb/{$this->tracking_number}",
            'pos'     => "https://www.posindonesia.co.id/en/tracking?barcode={$this->tracking_number}",
            default   => null,
        };
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('order_number', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }
}

