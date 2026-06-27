<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ── SIDEBAR FILTERS ──────────────────────────────────────────── --}}
        <aside class="lg:w-64 flex-shrink-0">
            <div class="border-2 border-white/10 p-6 sticky top-24">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-sm font-black uppercase tracking-widest">Filter</h2>
                    @if($category || !empty($sizes) || $sort !== 'latest')
                        <button wire:click="clearFilters" class="text-xs text-violet-400 hover:text-violet-300 transition-colors uppercase tracking-wider">
                            Reset
                        </button>
                    @endif
                </div>

                {{-- Category --}}
                <div class="mb-6">
                    <p class="label-gutta mb-3">Kategori</p>
                    <button wire:click="$set('category', '')"
                            class="block w-full text-left py-1.5 text-sm {{ !$category ? 'text-violet-400 font-bold' : 'text-zinc-400 hover:text-white' }} transition-colors">
                        Semua
                    </button>
                    @foreach($categories as $cat)
                        <button wire:click="$set('category', '{{ $cat->slug }}')"
                                class="block w-full text-left py-1.5 text-sm {{ $category === $cat->slug ? 'text-violet-400 font-bold' : 'text-zinc-400 hover:text-white' }} transition-colors">
                            {{ $cat->name }}
                            <span class="text-zinc-600 text-xs ml-1">({{ $cat->products_count }})</span>
                        </button>
                    @endforeach
                </div>

                {{-- Size --}}
                <div class="mb-6 border-t border-white/10 pt-6">
                    <p class="label-gutta mb-3">Ukuran</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($availableSizes as $size)
                            <button wire:click="$toggle('sizes', '{{ $size }}')"
                                    class="size-btn text-xs {{ in_array($size, $sizes) ? 'size-btn-selected' : '' }}">
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Sort --}}
                <div class="border-t border-white/10 pt-6">
                    <p class="label-gutta mb-3">Urutkan</p>
                    <select wire:model.live="sort" class="select-gutta">
                        <option value="latest">Terbaru</option>
                        <option value="price_asc">Harga Terendah</option>
                        <option value="price_desc">Harga Tertinggi</option>
                    </select>
                </div>
            </div>
        </aside>

        {{-- ── PRODUCT GRID ──────────────────────────────────────────────── --}}
        <div class="flex-1">
            {{-- Loading state --}}
            <div wire:loading.flex class="items-center justify-center py-20">
                <div class="w-6 h-6 border-2 border-violet-600 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <div wire:loading.remove>
                @if($products->total() > 0)
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-zinc-500 text-sm">
                            Menampilkan <span class="text-white font-bold">{{ $products->total() }}</span> produk
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-0 border-2 border-white/10">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="border-2 border-white/10 p-20 text-center">
                        <p class="text-zinc-500 text-lg mb-4">Tidak ada produk ditemukan.</p>
                        <button wire:click="clearFilters" class="btn-ghost">Reset Filter</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
