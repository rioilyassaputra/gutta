<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Order $order) {}

    public function handle(): void
    {
        Mail::send(
            'emails.order-confirmation',
            ['order' => $this->order->load(['items', 'user'])],
            function ($message) {
                $message
                    ->to($this->order->user->email, $this->order->user->name)
                    ->subject("Pesanan #{$this->order->order_number} Berhasil Dikonfirmasi — Gutta Store");
            }
        );
    }
}
