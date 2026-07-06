<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmationEmail;
use App\Jobs\SendShippingNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly RajaOngkirService $rajaOngkirService
    ) {}

    /**
     * Create an order from checkout data.
     * Validates prices & stock from DB — never trusts client data.
     */
    public function createFromCheckout(User $user, array $checkoutData): Order
    {
        return DB::transaction(function () use ($user, $checkoutData) {
            $cartItems = $this->cartService->getCartItems($user);

            if ($cartItems->isEmpty()) {
                throw new \RuntimeException('Keranjang belanja kosong.');
            }

            // 1. Validate stock with DB lock
            $stockValidation = $this->cartService->validateStock($cartItems);
            if (! $stockValidation['valid']) {
                throw new \RuntimeException(implode(', ', $stockValidation['errors']));
            }

            // 2. Re-calculate prices from DB (never trust client)
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($cartItems as $item) {
                // Lock variant for update to prevent race condition
                $variant = ProductVariant::lockForUpdate()->findOrFail($item->product_variant_id);

                if ($variant->stock < $item->qty) {
                    throw new \RuntimeException(
                        "{$variant->product->name} ({$variant->size}): stok tidak cukup."
                    );
                }

                $price    = (float) $variant->product->price + (float) $variant->additional_price;
                $itemSub  = $price * $item->qty;
                $subtotal += $itemSub;

                $orderItemsData[] = [
                    'product_variant_id' => $variant->id,
                    'product_name'       => $variant->product->name,
                    'variant_detail'     => $variant->detail,
                    'price'              => $price,
                    'qty'                => $item->qty,
                    'subtotal'           => $itemSub,
                ];

                // 3. Deduct stock
                $variant->decrement('stock', $item->qty);
            }

            // 3.5. Re-calculate and validate shipping cost from RajaOngkir Service (Anti-Tampering)
            $totalWeight = $this->cartService->getTotalWeight($cartItems);
            $addressSnapshot = $checkoutData['address_snapshot'] ?? null;
            if (!$addressSnapshot || !isset($addressSnapshot['city_id'])) {
                throw new \RuntimeException('Alamat pengiriman tidak valid.');
            }
            $destinationCityId = (int) $addressSnapshot['city_id'];

            $courier = $checkoutData['courier'] ?? null;
            $courierServiceName = $checkoutData['courier_service'] ?? null;

            if (!$courier || !$courierServiceName) {
                throw new \RuntimeException('Kurir dan layanan pengiriman wajib dipilih.');
            }

            // Dapatkan opsi ongkir dari server-side RajaOngkirService
            $shippingOptions = $this->rajaOngkirService->getCost($destinationCityId, max($totalWeight, 1));
            
            // Cari opsi ongkir yang dipilih user
            $matchedOption = null;
            foreach ($shippingOptions as $option) {
                if (strtolower($option['courier']) === strtolower($courier) &&
                    strtolower($option['service']) === strtolower($courierServiceName)) {
                    $matchedOption = $option;
                    break;
                }
            }

            if (!$matchedOption) {
                throw new \RuntimeException("Metode pengiriman {$courier} - {$courierServiceName} tidak tersedia.");
            }

            $shippingCost = (float) $matchedOption['cost'];
            $total        = $subtotal + $shippingCost;

            // 4. Create order
            $order = Order::create([
                'user_id'          => $user->id,
                'address_snapshot' => $addressSnapshot,
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'total'            => $total,
                'courier'          => $courier,
                'courier_service'  => $courierServiceName,
                'status'           => 'pending_payment',
            ]);

            // 5. Create order items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // 6. Clear cart
            $this->cartService->clearCart($user);

            return $order;
        });
    }

    /**
     * Mark order as paid after successful webhook verification.
     * Dispatches email confirmation to queue.
     */
    public function markAsPaid(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            SendOrderConfirmationEmail::dispatch($order)->onQueue('emails');
        });
    }

    /**
     * Update order tracking number and dispatch shipping notification.
     */
    public function updateTracking(Order $order, string $trackingNumber): void
    {
        DB::transaction(function () use ($order, $trackingNumber) {
            $order->update([
                'tracking_number' => $trackingNumber,
                'status'          => 'shipped',
                'shipped_at'      => now(),
            ]);

            SendShippingNotification::dispatch($order)->onQueue('emails');
        });
    }

    /**
     * Cancel order and restore stock.
     */
    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Lock the order row to prevent concurrency
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if (in_array($lockedOrder->status, ['paid', 'processing', 'shipped', 'completed'])) {
                throw new \RuntimeException('Pesanan tidak dapat dibatalkan.');
            }

            // Restore stock with row lock
            foreach ($lockedOrder->items as $item) {
                $variant = ProductVariant::where('id', $item->product_variant_id)
                    ->lockForUpdate()
                    ->first();
                if ($variant) {
                    $variant->increment('stock', $item->qty);
                }
            }

            $lockedOrder->update(['status' => 'cancelled']);
        });
    }
}
