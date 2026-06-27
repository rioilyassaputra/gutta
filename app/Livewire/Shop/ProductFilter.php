<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductFilter extends Component
{
    use WithPagination;

    #[Url(as: 'kategori')]
    public string $category = '';

    #[Url(as: 'ukuran')]
    public array $sizes = [];

    #[Url(as: 'urut')]
    public string $sort = 'latest';

    #[Url(as: 'min_harga')]
    public ?int $minPrice = null;

    #[Url(as: 'max_harga')]
    public ?int $maxPrice = null;

    public ?int $activeCategoryId = null;

    public function mount(?string $categorySlug = null): void
    {
        if ($categorySlug) {
            $this->category = $categorySlug;
        }
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSizes(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->category = '';
        $this->sizes = [];
        $this->sort = 'latest';
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::active()->with(['images', 'variants', 'category']);

        // Filter by category
        if ($this->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $this->category));
        }

        // Filter by sizes
        if (! empty($this->sizes)) {
            $query->whereHas('variants', fn($q) => $q->whereIn('size', $this->sizes)->where('stock', '>', 0));
        }

        // Filter by price
        if ($this->minPrice !== null) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice !== null) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Sorting
        match($this->sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->latest(),
        };

        $products = $query->paginate(12);

        $categories = Category::withCount(['products' => fn($q) => $q->active()])
            ->having('products_count', '>', 0)
            ->get();

        $availableSizes = ['S', 'M', 'L', 'XL', 'XXL'];

        return view('livewire.shop.product-filter', compact('products', 'categories', 'availableSizes'));
    }
}
