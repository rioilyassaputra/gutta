<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PakasirPaymentService implements PaymentServiceInterface
{
    protected string $slug;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->slug = config('pakasir.slug', 'gutta-store');
        $this->apiKey = config('pakasir.api_key', '');
        $this->baseUrl = config('pakasir.base_url', 'https://app.pakasir.com/api');
    }

    /**
     * Create a Pakasir transaction.
     */
    public function createTransaction(Order $order): array
    {
        $orderId = $order->order_number;
        $amount = (int) round($order->total);

        $redirectUrl = route('orders.show', $orderId);
        // Standard Pakasir Payment Link URL as fallback / direct redirect
        $fallbackPaymentUrl = "https://app.pakasir.com/pay/{$this->slug}/{$amount}?order_id={$orderId}&redirect_url=" . urlencode($redirectUrl);

        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/transactioncreate/qris", [
                'project'      => $this->slug,
                'order_id'     => $orderId,
                'amount'       => $amount,
                'api_key'      => $this->apiKey,
                'redirect_url' => $redirectUrl,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Get URL from API response or use fallback payment link
                $paymentUrl = $data['payment_url'] 
                    ?? $data['checkout_url'] 
                    ?? $data['redirect_url'] 
                    ?? $data['url'] 
                    ?? $fallbackPaymentUrl;

                return [
                    'success'      => true,
                    'payment_url'  => $paymentUrl,
                    'order_id'     => $orderId,
                    'raw_response' => $data,
                ];
            }

            Log::warning("Pakasir API transactioncreate returned status {$response->status()}: " . $response->body());
            
            // Return fallback payment link if API response is not standard but successful HTTP
            return [
                'success'     => true,
                'payment_url' => $fallbackPaymentUrl,
                'order_id'    => $orderId,
            ];
        } catch (\Exception $e) {
            Log::error('Pakasir createTransaction Exception: ' . $e->getMessage());

            // Still provide fallback URL if network issue or offline testing
            return [
                'success'     => true,
                'payment_url' => $fallbackPaymentUrl,
                'order_id'    => $orderId,
                'error'       => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify incoming webhook notification from Pakasir.
     */
    public function verifyWebhook(array $payload, string $signature = ''): bool
    {
        // 1. Verify project slug matches
        $payloadProject = $payload['project'] ?? null;
        if ($payloadProject && strtolower($payloadProject) !== strtolower($this->slug)) {
            Log::warning("Pakasir Webhook Mismatch: project {$payloadProject} does not match {$this->slug}");
            return false;
        }

        // 2. Ensure order_id and status/amount exist
        if (empty($payload['order_id'])) {
            Log::warning("Pakasir Webhook: missing order_id");
            return false;
        }

        return true;
    }

    /**
     * Check transaction status directly via Pakasir API.
     */
    public function getTransactionStatus(Order|string $order, int $amount = 0): ?string
    {
        $orderId = $order instanceof Order ? $order->order_number : $order;
        $orderAmount = $order instanceof Order ? (int) round($order->total) : $amount;

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/transactiondetail", [
                'project'  => $this->slug,
                'order_id' => $orderId,
                'amount'   => $orderAmount,
                'api_key'  => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['transaction']['status'] ?? $data['status'] ?? $data['transaction_status'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Pakasir getTransactionStatus error for Order {$orderId}: " . $e->getMessage());
            return null;
        }
    }
}
