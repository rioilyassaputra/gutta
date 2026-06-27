<x-app-layout>
    <x-slot:title>{{ isset($category) ? $category->name . ' — Shop' : 'Shop All' }}</x-slot:title>
    <x-slot:metaDescription>Browse koleksi apparel streetwear premium Gutta. T-Shirt, Hoodie, Pants dan lebih banyak lagi.</x-slot:metaDescription>

    {{-- Page Header --}}
    <div class="border-b-2 border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <p class="section-subtitle mb-2 text-violet-400">Gutta Store</p>
            <h1 class="section-title text-5xl">
                {{ isset($category) ? strtoupper($category->name) : 'SHOP ALL' }}
            </h1>
        </div>
    </div>

    {{-- Livewire Product Filter Component --}}
    <livewire:shop.product-filter :categorySlug="isset($category) ? $category->slug : null" />
</x-app-layout>
