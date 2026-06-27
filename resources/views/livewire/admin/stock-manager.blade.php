<div class="space-y-6">
    {{-- Search Bar --}}
    <div class="bg-zinc-950 p-6 border-2 border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
            <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Cari Produk / SKU</label>
            <input type="text" wire:model.live.debounce.300ms="search" 
                   class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                   placeholder="Cari berdasarkan nama produk, slug, atau SKU variant...">
        </div>
    </div>

    {{-- Stock Listing --}}
    <div class="border-2 border-white/10 bg-zinc-950 overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <h2 class="text-lg font-black uppercase tracking-wider text-white">Stok Produk & Varian</h2>
            <p class="text-zinc-500 text-xs mt-1">Kelola stok untuk setiap ukuran/varian produk secara real-time.</p>
        </div>

        <div class="divide-y divide-white/10">
            @forelse($products as $product)
                <div class="p-6 flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
                    {{-- Product Info --}}
                    <div class="flex gap-4 items-start max-w-md">
                        <div class="w-16 h-16 bg-zinc-900 border border-white/10 overflow-hidden flex-shrink-0">
                            <img src="{{ $product->primaryImage?->webp_url ?? asset('images/logo.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-base">{{ $product->name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-bold text-violet-400 bg-violet-950/20 px-2 py-0.5">{{ $product->category->name }}</span>
                                <span class="text-xs {{ $product->is_active ? 'text-green-500' : 'text-red-500' }}">
                                    ● {{ $product->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Variants and stock inputs --}}
                    <div class="flex-1 w-full lg:max-w-2xl border-t lg:border-t-0 border-white/5 pt-4 lg:pt-0 space-y-3">
                        @foreach($product->variants as $variant)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-black/40 p-3 border border-white/5">
                                <div class="font-mono text-xs">
                                    <p class="font-bold text-white">Size: {{ $variant->size }}</p>
                                    <p class="text-zinc-500 mt-0.5">SKU: {{ $variant->sku }}</p>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-center">
                                    <span class="text-xs text-zinc-500">Stok:</span>
                                    <input type="number" 
                                           wire:model="editingStock.{{ $variant->id }}" 
                                           class="w-20 bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-1 px-2 text-sm text-center focus:outline-none transition-colors"
                                           min="0">
                                    <button type="button" 
                                            wire:click="updateStock({{ $variant->id }})" 
                                            class="bg-violet-700 hover:bg-white text-white hover:text-black font-bold uppercase tracking-wider text-xs px-3 py-1.5 transition-colors">
                                        Update
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-zinc-500 text-sm">
                    Tidak ada produk ditemukan.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $products->links() }}
    </div>
</div>
