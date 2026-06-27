<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class AddToCart extends Component
{
    public Product $product;
    public ?int $selectedVariantId = null;
    public int $qty = 1;
    public string $message = '';
    public string $messageType = 'success';

    public function mount(Product $product): void
    {
        $this->product = $product->load(['variants', 'images']);
    }

    public function selectVariant(int $variantId): void
    {
        $this->selectedVariantId = $variantId;
        $this->message = '';
    }

    public function incrementQty(): void
    {
        if ($this->selectedVariantId) {
            $variant = ProductVariant::find($this->selectedVariantId);
            if ($variant && $this->qty < $variant->stock) {
                $this->qty++;
            }
        }
    }

    public function decrementQty(): void
    {
        if ($this->qty > 1) {
            $this->qty--;
        }
    }

    public function addToCart(CartService $cartService): void
    {
        if (! $this->selectedVariantId) {
            $this->message = 'Pilih ukuran terlebih dahulu.';
            $this->messageType = 'error';
            return;
        }

        $variant = ProductVariant::find($this->selectedVariantId);
        if (! $variant) {
            $this->message = 'Varian tidak ditemukan.';
            $this->messageType = 'error';
            return;
        }

        $user = auth()->user();
        $sessionId = session()->getId();

        $result = $cartService->addItem($user, $variant, $this->qty, $sessionId);

        $this->message = $result['message'];
        $this->messageType = $result['success'] ? 'success' : 'error';

        if ($result['success']) {
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        return view('livewire.shop.add-to-cart');
    }
}
