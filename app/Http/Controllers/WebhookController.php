<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransPaymentService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function midtrans(
        Request $request,
        OrderService $orderService,
        MidtransPaymentService $paymentService
    ): Response {
        $payload = $request->all();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // 1. WAJIB: Verify HMAC-SHA512 signature
        $signature = $request->input('signature_key', '');
        
        if (empty($signature)) {
            Log::warning("Midtrans Webhook: Request missing signature key. IP: {$ipAddress}, UA: {$userAgent}");
            return response('Missing signature key', 400);
        }

        if (! $paymentService->verifyWebhook($payload, $signature)) {
            Log::warning("Midtrans Webhook: Invalid signature key. IP: {$ipAddress}, UA: {$userAgent}, Payload: " . json_encode($payload));
            return response('Unauthorized', 400);
        }

        Log::info("Midtrans Webhook: Valid signature verified for Order ID: " . ($payload['order_id'] ?? 'N/A'));

        $orderIdPayload = $request->input('order_id', '');
        // Extract base order number (GTT-YYYYMMDD-XXXXX-timestamp -> GTT-YYYYMMDD-XXXXX)
        $parts = explode('-', $orderIdPayload);
        $orderId = implode('-', array_slice($parts, 0, 3));

        $transactionStatus = $request->input('transaction_status');

        try {
            return DB::transaction(function () use ($orderId, $transactionStatus, $orderService) {
                // 2. Find order by order_number with DB row lock (lockForUpdate) to prevent race conditions
                $order = Order::where('order_number', $orderId)
                    ->lockForUpdate()
                    ->first();

                if (! $order) {
                    Log::warning("Midtrans Webhook: Order not found: {$orderId}");
                    return response('Order not found', 404);
                }

                // 3. Idempotency check — skip if already paid or cancelled
                if ($order->status === 'paid') {
                    Log::info("Midtrans Webhook: Order {$orderId} is already paid. Skipping.");
                    return response('OK', 200);
                }

                if ($order->status === 'cancelled') {
                    Log::info("Midtrans Webhook: Order {$orderId} is already cancelled. Skipping.");
                    return response('OK', 200);
                }

                // 4. Process by transaction status
                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    $orderService->markAsPaid($order);
                    Log::info("Midtrans Webhook: Order {$orderId} successfully marked as PAID.");
                } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {
                    $orderService->cancelOrder($order);
                    Log::info("Midtrans Webhook: Order {$orderId} successfully CANCELLED and stock restored.");
                }

                return response('OK', 200);
            });
        } catch (\Exception $e) {
            Log::error("Midtrans Webhook: Error processing order {$orderId}. Message: " . $e->getMessage());
            return response('Error', 500);
        }
    }
}
