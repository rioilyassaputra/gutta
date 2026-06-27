<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'district',
        'postal_code',
        'full_address',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'province_id' => 'integer',
        'city_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toSnapshot(): array
    {
        return [
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'label' => $this->label,
            'province_name' => $this->province_name,
            'city_name' => $this->city_name,
            'district' => $this->district,
            'postal_code' => $this->postal_code,
            'full_address' => $this->full_address,
            'city_id' => $this->city_id,
        ];
    }
}
