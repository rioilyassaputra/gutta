@props(['product'])

<a href="{{ route('shop.products.show', $product->slug) }}"
   class="group relative block border-r border-b border-white/10 hover:border-r-violet-600 hover:border-b-violet-600 bg-black overflow-hidden transition-colors duration-150"
   id="product-card-{{ $product->id }}">

    {{-- Product Image with hover swap --}}
    <div class="relative aspect-square overflow-hidden bg-zinc-950">
        {{-- Primary Image --}}
        @if($product->primaryImage)
            <img src="{{ $product->primaryImage->webp_url }}"
                 alt="{{ $product->name }}"
                 loading="lazy"
                 class="w-full h-full object-cover absolute inset-0
                        transition-opacity duration-150 opacity-100 group-hover:opacity-0">
        @else
            <div class="w-full h-full bg-zinc-900 flex items-center justify-center absolute inset-0">
                <span class="text-zinc-700 text-xs uppercase tracking-widest">No Image</span>
            </div>
        @endif

        {{-- Secondary Image (hover) --}}
        @if($product->secondaryImage || $product->primaryImage)
            <img src="{{ ($product->secondaryImage ?? $product->primaryImage)->webp_url }}"
                 alt="{{ $product->name }} — tampak belakang"
                 loading="lazy"
                 class="w-full h-full object-cover absolute inset-0
                        transition-opacity duration-150 opacity-0 group-hover:opacity-100">
        @endif

        {{-- Sold Out Badge --}}
        @if($product->isSoldOut())
            <div class="absolute inset-0 bg-black/70 flex items-center justify-center">
                <span class="text-xs font-black uppercase tracking-[0.3em] border-2 border-white/40 px-4 py-2 text-white/60">
                    SOLD OUT
                </span>
            </div>
        @endif

        {{-- Purple corner accent on hover --}}
        <div class="absolute bottom-0 right-0 w-0 h-0 group-hover:w-8 group-hover:h-8
                    border-b-2 border-r-2 border-violet-600 transition-all duration-200"></div>
    </div>

    {{-- Product Info --}}
    <div class="p-4 border-t border-white/10">
        <p class="text-xs text-zinc-500 uppercase tracking-widest mb-1">
            {{ $product->category?->name ?? 'Uncategorized' }}
        </p>
        <h3 class="font-bold text-white group-hover:text-violet-400 transition-colors duration-150 text-sm leading-tight truncate">
            {{ $product->name }}
        </h3>
        <p class="text-white font-black mt-2 text-base">
            {{ $product->formatted_price }}
        </p>
    </div>
</a>
