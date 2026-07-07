<?php

namespace App\Livewire\Checkout;

use App\Models\Address;
use App\Services\CartService;
use App\Services\MidtransPaymentService;
use App\Services\OrderService;
use App\Services\RajaOngkirService;
use Livewire\Component;

class CheckoutWizard extends Component
{
    // Steps: 1=Address, 2=Shipping, 3=Confirmation
    public int $step = 1;

    // Step 1
    public ?int $selectedAddressId = null;

    // Step 2
    public array $shippingOptions = [];
    public ?string $selectedCourier = null;
    public ?string $selectedCourierService = null;
    public float $selectedShippingCost = 0;
    public string $selectedCourierName = '';
    public bool $loadingShipping = false;

    // Step 3
    public string $snapToken = '';

    public function mount(): void
    {
        $user = auth()->user();
        $defaultAddress = $user->defaultAddress();
        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
        }
    }

    // ── Step 1: Address ──────────────────────────────────────────────────
    public function goToShipping(): void
    {
        if (! $this->selectedAddressId) {
            $this->dispatch('notify', message: 'Pilih alamat pengiriman terlebih dahulu.', type: 'error');
            return;
        }

        $this->step = 2;
        $this->loadShippingOptions();
    }

    // ── Step 2: Shipping ─────────────────────────────────────────────────
    public function loadShippingOptions(): void
    {
        $this->loadingShipping = true;
        $this->shippingOptions = [];

        $address = Address::find($this->selectedAddressId);
        if (! $address) {
            $this->loadingShipping = false;
            return;
        }

        $cartService = app(CartService::class);
        $cartItems = $cartService->getCartItems(auth()->user());
        $totalWeight = $cartService->getTotalWeight($cartItems);

        $rajaOngkir = app(RajaOngkirService::class);
        $this->shippingOptions = $rajaOngkir->getCost($address->city_id, max($totalWeight, 1));

        $this->loadingShipping = false;
    }

    public function selectShipping(string $courier, string $service, float $cost, string $name): void
    {
        $this->selectedCourier = $courier;
        $this->selectedCourierService = $service;
        $this->selectedShippingCost = $cost;
        $this->selectedCourierName = $name;
    }

    public function goToConfirmation(): void
    {
        if (! $this->selectedCourier) {
            $this->dispatch('notify', message: 'Pilih metode pengiriman terlebih dahulu.', type: 'error');
            return;
        }
        $this->step = 3;
    }

    // ── Step 3: Payment ──────────────────────────────────────────────────
    public function createOrder(OrderService $orderService, \App\Services\PaymentServiceInterface $paymentService)
    {
        $address = Address::findOrFail($this->selectedAddressId);

        try {
            $order = $orderService->createFromCheckout(auth()->user(), [
                'address_snapshot' => $address->toSnapshot(),
                'courier'          => $this->selectedCourier,
                'courier_service'  => $this->selectedCourierService,
            ]);

            $result = $paymentService->createTransaction($order);

            if (!empty($result['success']) && !empty($result['payment_url'])) {
                $paymentUrl = $result['payment_url'];
                $order->update(['payment_token' => $paymentUrl]);

                return $this->redirect($paymentUrl, navigate: false);
            } else {
                $this->dispatch('notify', message: 'Gagal membuat transaksi pembayaran. Coba lagi.', type: 'error');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }


    public function backToStep(int $step): void
    {
        $this->step = $step;
    }

    public function render(CartService $cartService)
    {
        $user = auth()->user();
        $addresses = $user->addresses()->withoutTrashed()->get();
        $cartItems = $cartService->getCartItems($user);

        $subtotal = $cartItems->sum(function ($item) {
            return (float) $item->variant->getFinalPriceAttribute() * $item->qty;
        });

        $total = $subtotal + $this->selectedShippingCost;

        return view('livewire.checkout.checkout-wizard', compact(
            'addresses', 'cartItems', 'subtotal', 'total'
        ));
    }
}
