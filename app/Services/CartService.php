<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Add item to cart. If variant already exists, increment qty.
     * Always validates stock server-side.
     */
    public function addItem(?User $user, ProductVariant $variant, int $qty = 1, ?string $sessionId = null): array
    {
        if ($variant->stock < $qty) {
            return ['success' => false, 'message' => 'Stok tidak mencukupi.'];
        }

        $query = $this->baseQuery($user, $sessionId);
        $existing = $query->where('product_variant_id', $variant->id)->first();

        if ($existing) {
            $newQty = $existing->qty + $qty;
            if ($newQty > $variant->stock) {
                return ['success' => false, 'message' => "Stok hanya tersisa {$variant->stock} item."];
            }
            $existing->update(['qty' => $newQty]);
        } else {
            CartItem::create([
                'user_id'            => $user?->id,
                'session_id'         => $user ? null : $sessionId,
                'product_variant_id' => $variant->id,
                'qty'                => $qty,
            ]);
        }

        return ['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang!'];
    }

    /**
     * Get cart items for user or guest session.
     */
    public function getCartItems(?User $user, ?string $sessionId = null): Collection
    {
        return $this->baseQuery($user, $sessionId)
            ->with(['variant.product.images', 'variant.product.category'])
            ->get();
    }

    /**
     * Update item quantity.
     */
    public function updateQty(?User $user, int $cartItemId, int $qty, ?string $sessionId = null): array
    {
        $item = $this->baseQuery($user, $sessionId)->where('id', $cartItemId)->firstOrFail();

        if ($qty <= 0) {
            $item->delete();
            return ['success' => true, 'message' => 'Item dihapus dari keranjang.'];
        }

        $variant = $item->variant;
        if ($qty > $variant->stock) {
            return ['success' => false, 'message' => "Stok hanya tersisa {$variant->stock} item."];
        }

        $item->update(['qty' => $qty]);
        return ['success' => true, 'message' => 'Jumlah diperbarui.'];
    }

    /**
     * Remove a specific item from cart.
     */
    public function removeItem(?User $user, int $cartItemId, ?string $sessionId = null): void
    {
        $this->baseQuery($user, $sessionId)->where('id', $cartItemId)->delete();
    }

    /**
     * Clear all items from cart.
     */
    public function clearCart(?User $user, ?string $sessionId = null): void
    {
        $this->baseQuery($user, $sessionId)->delete();
    }

    /**
     * Merge guest cart (session) into authenticated user cart on login.
     */
    public function mergeGuestCart(User $user, string $sessionId): void
    {
        $guestItems = CartItem::where('session_id', $sessionId)->get();

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::where('user_id', $user->id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existing) {
                $newQty = min($existing->qty + $guestItem->qty, $guestItem->variant->stock);
                $existing->update(['qty' => $newQty]);
                $guestItem->delete();
            } else {
                $guestItem->update(['user_id' => $user->id, 'session_id' => null]);
            }
        }
    }

    /**
     * Validate stock for all cart items before checkout.
     * Returns ['valid' => bool, 'errors' => [...]]
     */
    public function validateStock(Collection $cartItems): array
    {
        $errors = [];

        foreach ($cartItems as $item) {
            $variant = ProductVariant::find($item->product_variant_id);
            if (! $variant || $variant->stock < $item->qty) {
                $available = $variant ? $variant->stock : 0;
                $errors[] = "{$item->variant->product->name} ({$item->variant->size}): stok hanya {$available}.";
            }
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }

    /**
     * Count total items in cart.
     */
    public function getCount(?User $user, ?string $sessionId = null): int
    {
        return $this->baseQuery($user, $sessionId)->sum('qty');
    }

    /**
     * Calculate total weight of cart items in grams.
     */
    public function getTotalWeight(Collection $cartItems): int
    {
        return $cartItems->sum(function ($item) {
            return $item->variant->product->weight_grams * $item->qty;
        });
    }

    private function baseQuery(?User $user, ?string $sessionId)
    {
        $query = CartItem::query();

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query;
    }
}
