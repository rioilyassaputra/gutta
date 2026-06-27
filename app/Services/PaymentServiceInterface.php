<?php

namespace App\Services;

use App\Models\Order;

interface PaymentServiceInterface
{
    /**
     * Create a payment transaction and return token/redirect data.
     */
    public function createTransaction(Order $order): array;

    /**
     * Verify webhook signature from payment gateway.
     */
    public function verifyWebhook(array $payload, string $signature): bool;
}
