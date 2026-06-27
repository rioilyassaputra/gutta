<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    /**
     * Auto-generate order_number in format: GTT-YYYYMMDD-XXXXX
     */
    public function creating(Order $order): void
    {
        if (empty($order->order_number)) {
            $order->order_number = $this->generateOrderNumber();
        }
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "GTT-{$date}-";

        // Get the latest order for today to determine sequence
        $latest = Order::withTrashed()
            ->where('order_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        if ($latest) {
            $lastSeq = (int) substr($latest->order_number, -5);
            $seq = str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $seq = '00001';
        }

        return $prefix . $seq;
    }
}
