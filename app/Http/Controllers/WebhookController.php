<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransPaymentService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    public function midtrans(
        Request $request,
        OrderService $orderService,
        MidtransPaymentService $paymentService
    ): Response {
        $payload = $request->all();

        // 1. WAJIB: Verify HMAC-SHA512 signature
        $signature = $request->input('signature_key', '');
        if (! $paymentService->verifyWebhook($payload, $signature)) {
            return response('Unauthorized', 400);
        }

        // 2. Find order by order_number
        $order = Order::where('order_number', $request->input('order_id'))->first();
        if (! $order) {
            return response('Order not found', 404);
        }

        // 3. Idempotency check — skip if already paid
        if ($order->status === 'paid') {
            return response('OK', 200);
        }

        // 4. Process by transaction status
        $transactionStatus = $request->input('transaction_status');

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            $orderService->markAsPaid($order);
        } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {
            $order->update(['status' => 'cancelled']);
        }

        return response('OK', 200);
    }
}
