<x-app-layout>
    <x-slot:title>{{ $product->name }}</x-slot:title>
    <x-slot:metaDescription>{{ Str::limit($product->description, 155) }}</x-slot:metaDescription>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-zinc-500 mb-8 uppercase tracking-wider">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('shop.products.index') }}" class="hover:text-white transition-colors">Shop</a>
            <span>/</span>
            @if($product->category)
                <a href="{{ route('shop.collections.show', $product->category->slug) }}" class="hover:text-white transition-colors">{{ $product->category->name }}</a>
                <span>/</span>
            @endif
            <span class="text-violet-400">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 border-2 border-white/10">
            {{-- ── Product Images ────────────────────────────────────── --}}
            <div class="border-r border-white/10" x-data="{ active: 0 }">
                {{-- Main Image --}}
                <div class="aspect-square bg-zinc-950 relative overflow-hidden">
                    @foreach($product->images as $i => $image)
                        <img src="{{ $image->webp_url }}"
                             alt="{{ $product->name }}"
                             x-show="active === {{ $i }}"
                             loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                             class="w-full h-full object-cover absolute inset-0">
                    @endforeach

                    @if($product->images->isEmpty())
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-zinc-700 text-sm uppercase tracking-widest">No Image</span>
                        </div>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if($product->images->count() > 1)
                    <div class="flex gap-0 border-t-2 border-white/10">
                        @foreach($product->images as $i => $image)
                            <button @click="active = {{ $i }}"
                                    class="w-20 h-20 flex-shrink-0 border-r border-white/10
                                           hover:border-violet-600 transition-colors overflow-hidden"
                                    :class="active === {{ $i }} ? 'border-violet-600' : ''">
                                <img src="{{ $image->webp_url }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Product Info ──────────────────────────────────────── --}}
            <div class="p-8 lg:p-12 flex flex-col">
                <p class="text-violet-400 text-xs uppercase tracking-widest mb-3">
                    {{ $product->category?->name }}
                </p>
                <h1 class="text-3xl font-black uppercase tracking-tight font-display mb-4">
                    {{ $product->name }}
                </h1>
                <p class="text-4xl font-black text-white mb-6">
                    {{ $product->formatted_price }}
                </p>

                @if($product->description)
                    <p class="text-zinc-400 text-sm leading-relaxed mb-8 border-t border-b border-white/10 py-6">
                        {{ $product->description }}
                    </p>
                @endif

                {{-- Add to Cart Livewire Component --}}
                <livewire:shop.add-to-cart :product="$product" />

                {{-- Size Chart Accordion --}}
                <div class="mt-8 border-t border-white/10" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between py-4 text-sm font-bold uppercase tracking-wider hover:text-violet-400 transition-colors">
                        <span>Size Chart</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pb-4">
                        <table class="w-full text-sm text-center border border-white/10">
                            <thead>
                                <tr class="border-b border-white/10 bg-zinc-900">
                                    <th class="py-2 px-4 text-xs font-bold uppercase tracking-wider text-zinc-400">Size</th>
                                    <th class="py-2 px-4 text-xs font-bold uppercase tracking-wider text-zinc-400">Dada (cm)</th>
                                    <th class="py-2 px-4 text-xs font-bold uppercase tracking-wider text-zinc-400">Panjang (cm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-white/10"><td class="py-2">S</td><td class="py-2">96</td><td class="py-2">68</td></tr>
                                <tr class="border-b border-white/10"><td class="py-2">M</td><td class="py-2">100</td><td class="py-2">70</td></tr>
                                <tr class="border-b border-white/10"><td class="py-2">L</td><td class="py-2">104</td><td class="py-2">72</td></tr>
                                <tr class="border-b border-white/10"><td class="py-2">XL</td><td class="py-2">108</td><td class="py-2">74</td></tr>
                                <tr><td class="py-2">XXL</td><td class="py-2">112</td><td class="py-2">76</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Shipping info --}}
                <div class="mt-4 flex items-center gap-3 text-xs text-zinc-500">
                    <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Dikirim dari Boyolali, Jawa Tengah
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if($related->isNotEmpty())
            <div class="mt-20">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="section-title text-3xl">PRODUK TERKAIT</h2>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-0 border-2 border-white/10">
                    @foreach($related as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
