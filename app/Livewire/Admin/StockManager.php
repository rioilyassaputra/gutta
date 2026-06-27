<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StockManager extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public array $editingStock = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updateStock(int $variantId): void
    {
        $newStock = $this->editingStock[$variantId] ?? null;

        if ($newStock === null || $newStock === '') {
            $this->dispatch('notify', message: 'Stok tidak boleh kosong.', type: 'error');
            return;
        }

        $newStock = (int) $newStock;
        if ($newStock < 0) {
            $this->dispatch('notify', message: 'Stok tidak boleh bernilai negatif.', type: 'error');
            return;
        }

        $variant = ProductVariant::findOrFail($variantId);
        $variant->update(['stock' => $newStock]);

        $this->dispatch('stock-updated');
        $this->dispatch('notify', message: "Stok variant {$variant->sku} berhasil diubah menjadi {$newStock}.", type: 'success');
    }

    public function render()
    {
        $query = Product::with(['variants', 'category'])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%")
                  ->orWhereHas('variants', fn($v) => $v->where('sku', 'like', "%{$this->search}%"));
            });
        }

        $products = $query->paginate(15);

        // Pre-fill editing array
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                if (!isset($this->editingStock[$variant->id])) {
                    $this->editingStock[$variant->id] = $variant->stock;
                }
            }
        }

        return view('livewire.admin.stock-manager', compact('products'));
    }
}
