<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class MidtransPaymentService implements PaymentServiceInterface
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;
    }

    /**
     * Create a Midtrans Snap transaction and return the snap token.
     */
    public function createTransaction(Order $order): array
    {
        $address = $order->address_snapshot;

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $address['recipient_name'] ?? $order->user->name,
                'email'      => $order->user->email,
                'phone'      => $address['phone'] ?? $order->user->whatsapp,
                'shipping_address' => [
                    'first_name' => $address['recipient_name'] ?? '',
                    'phone'      => $address['phone'] ?? '',
                    'address'    => $address['full_address'] ?? '',
                    'city'       => $address['city_name'] ?? '',
                    'postal_code'=> $address['postal_code'] ?? '',
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $this->buildItemDetails($order),
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'hour',
                'duration'   => 24,
            ],
            'callbacks' => [
                'finish' => url('/dashboard'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return ['success' => true, 'snap_token' => $snapToken];
        } catch (\Exception $e) {
            Log::error('Midtrans createTransaction error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify Midtrans webhook signature using HMAC-SHA512.
     */
    public function verifyWebhook(array $payload, string $signature): bool
    {
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash(
            'sha512',
            ($payload['order_id'] ?? '') .
            ($payload['status_code'] ?? '') .
            ($payload['gross_amount'] ?? '') .
            $serverKey
        );

        return hash_equals($expectedSignature, $signature);
    }

    private function buildItemDetails(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id'       => $item->product_variant_id,
                'price'    => (int) $item->price,
                'quantity' => $item->qty,
                'name'     => substr($item->product_name . ' (' . $item->variant_detail . ')', 0, 50),
            ];
        }

        // Add shipping as item
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id'       => 'SHIPPING',
                'price'    => (int) $order->shipping_cost,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim (' . strtoupper($order->courier) . ' ' . $order->courier_service . ')',
            ];
        }

        return $items;
    }
}
