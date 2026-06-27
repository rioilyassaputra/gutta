<?php

namespace App\Livewire\Components;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public int $count = 0;

    public function mount(CartService $cartService): void
    {
        $this->updateCount($cartService);
    }

    #[On('cart-updated')]
    public function refresh(CartService $cartService): void
    {
        $this->updateCount($cartService);
    }

    private function updateCount(CartService $cartService): void
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        $this->count = $cartService->getCount($user, $sessionId);
    }

    public function render()
    {
        return view('livewire.components.cart-count');
    }
}
