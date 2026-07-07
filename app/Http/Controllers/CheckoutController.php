<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(CartService $cartService)
    {
        $cartItems = $cartService->getCartItems(auth()->user());

        if ($cartItems->isEmpty()) {
            return redirect()->route('dashboard');
        }

        return view('pages.checkout.index');
    }
}
