<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransPaymentService;
use App\Services\OrderService;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(
        Order $order,
        MidtransPaymentService $paymentService,
        OrderService $orderService
    ): View {
        // Ensure user can only see their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // If pending payment, verify status against Midtrans API (fallback) & regenerate token if expired
        if ($order->status === 'pending_payment') {
            $parts = explode('|', $order->payment_token ?? '');
            $snapToken = $parts[0] ?? null;
            $uniqueOrderId = $parts[1] ?? null;

            // 1. Fallback status check: check if the transaction is already paid in Midtrans
            $isPaid = false;
            if ($uniqueOrderId) {
                $midtransStatus = $paymentService->getTransactionStatus($uniqueOrderId);
                if (in_array($midtransStatus, ['settlement', 'capture'])) {
                    $orderService->markAsPaid($order);
                    $order->refresh(); // Refresh order status
                    $isPaid = true;
                }
            }

            // 2. If still pending, regenerate token to prevent expiration issues
            if (!$isPaid && $order->status === 'pending_payment') {
                $result = $paymentService->createTransaction($order);
                if ($result['success']) {
                    $order->update(['payment_token' => $result['snap_token'] . '|' . $result['unique_order_id']]);
                }
            }
        }

        $order->load(['items.variant.product.images', 'user']);

        return view('pages.dashboard.orders.show', compact('order'));
    }
}
