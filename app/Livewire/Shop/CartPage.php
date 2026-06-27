<?php

namespace App\Livewire\Shop;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPage extends Component
{
    public function removeItem(int $itemId, CartService $cartService): void
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        $cartService->removeItem($user, $itemId, $sessionId);
        $this->dispatch('cart-updated');
    }

    public function updateQty(int $itemId, int $qty, CartService $cartService): void
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        $result = $cartService->updateQty($user, $itemId, $qty, $sessionId);

        if (! $result['success']) {
            $this->dispatch('notify', message: $result['message'], type: 'error');
        }

        $this->dispatch('cart-updated');
    }

    public function clearCart(CartService $cartService): void
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        $cartService->clearCart($user, $sessionId);
        $this->dispatch('cart-updated');
    }

    public function render(CartService $cartService)
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        $cartItems = $cartService->getCartItems($user, $sessionId);

        $subtotal = $cartItems->sum(function ($item) {
            return (float) $item->variant->getFinalPriceAttribute() * $item->qty;
        });

        return view('livewire.shop.cart-page', compact('cartItems', 'subtotal'));
    }
}
