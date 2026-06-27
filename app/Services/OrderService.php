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
        private readonly CartService $cartService
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

            $shippingCost = (float) ($checkoutData['shipping_cost'] ?? 0);
            $total        = $subtotal + $shippingCost;

            // 4. Create order
            $order = Order::create([
                'user_id'          => $user->id,
                'address_snapshot' => $checkoutData['address_snapshot'],
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'total'            => $total,
                'courier'          => $checkoutData['courier'],
                'courier_service'  => $checkoutData['courier_service'],
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
            if (in_array($order->status, ['paid', 'processing', 'shipped', 'completed'])) {
                throw new \RuntimeException('Pesanan tidak dapat dibatalkan.');
            }

            // Restore stock
            foreach ($order->items as $item) {
                ProductVariant::where('id', $item->product_variant_id)
                    ->increment('stock', $item->qty);
            }

            $order->update(['status' => 'cancelled']);
        });
    }
}
