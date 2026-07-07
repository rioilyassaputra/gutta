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
        \App\Services\PaymentServiceInterface $paymentService,
        OrderService $orderService
    ): View {
        // Ensure user can only see their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // If pending payment, verify status against Pakasir status API (fallback) & regenerate payment link if empty
        if ($order->status === 'pending_payment') {
            $status = method_exists($paymentService, 'getTransactionStatus') 
                ? $paymentService->getTransactionStatus($order) 
                : null;

            if (in_array(strtolower($status ?? ''), ['completed', 'paid', 'settlement', 'success'])) {
                $orderService->markAsPaid($order);
                $order->refresh();
            } else {
                $currentSlug = config('pakasir.slug', 'gutta');
                if (!empty($order->payment_token) && str_contains($order->payment_token, 'gutta-store')) {
                    $newToken = str_replace('gutta-store', $currentSlug, $order->payment_token);
                    $order->update(['payment_token' => $newToken]);
                } elseif (empty($order->payment_token)) {
                    $result = $paymentService->createTransaction($order);
                    if (!empty($result['payment_url'])) {
                        $order->update(['payment_token' => $result['payment_url']]);
                    }
                }
            }
        }


        $order->load(['items.variant.product.images', 'user']);

        return view('pages.dashboard.orders.show', compact('order'));
    }
}
