<div x-data="{ selectedVariant: $wire.entangle('selectedVariantId'), qty: $wire.entangle('qty') }">
    {{-- Size Selection --}}
    <div class="mb-6">
        <p class="label-gutta mb-3">Pilih Ukuran <span class="text-red-500">*</span></p>
        <div class="flex flex-wrap gap-2">
            @foreach($product->variants as $variant)
                <button type="button"
                        wire:click="selectVariant({{ $variant->id }})"
                        @click="selectedVariant = {{ $variant->id }}"
                        @if($variant->stock === 0) disabled @endif
                        id="variant-{{ $variant->id }}"
                        :class="selectedVariant === {{ $variant->id }} ? 'size-btn-selected' : ''"
                        class="size-btn {{ $variant->stock === 0 ? 'size-btn-disabled' : '' }}">
                    {{ $variant->size }}
                </button>
            @endforeach
        </div>

        {{-- Stock indicator --}}
        @if($selectedVariantId)
            @php
                $selectedVariant = $product->variants->firstWhere('id', $selectedVariantId);
            @endphp
            @if($selectedVariant && $selectedVariant->stock <= 5 && $selectedVariant->stock > 0)
                <p class="text-orange-400 text-xs mt-2 font-bold uppercase tracking-wider">
                    ⚡ Sisa {{ $selectedVariant->stock }} item
                </p>
            @endif
        @endif
    </div>

    {{-- Qty Selector --}}
    <div class="mb-6">
        <p class="label-gutta mb-3">Jumlah</p>
        <div class="flex items-center gap-0 border-2 border-zinc-700 w-fit">
            <button type="button"
                    wire:click="decrementQty"
                    @click="if(qty > 1) qty--"
                    class="w-10 h-10 flex items-center justify-center hover:bg-white/10 transition-colors text-lg font-bold">
                −
            </button>
            <span class="w-12 text-center font-bold text-sm" x-text="qty"></span>
            <button type="button"
                    wire:click="incrementQty"
                    @click="qty++"
                    class="w-10 h-10 flex items-center justify-center hover:bg-white/10 transition-colors text-lg font-bold">
                +
            </button>
        </div>
    </div>

    {{-- Feedback message --}}
    @if($message)
        <div class="mb-4 px-4 py-3 text-sm font-bold border-2
                    {{ $messageType === 'success' ? 'border-green-600 text-green-400 bg-green-600/10' : 'border-red-600 text-red-400 bg-red-600/10' }}">
            {{ $message }}
        </div>
    @endif

    {{-- Add to Cart Button --}}
    <div class="flex gap-3">
        <button wire:click="addToCart"
                wire:loading.attr="disabled"
                wire:target="addToCart"
                class="btn-primary flex-1 text-base py-4">
            <span wire:loading.remove wire:target="addToCart" class="inline-flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Tambah ke Keranjang
            </span>
            <span wire:loading.inline-flex wire:target="addToCart" class="items-center justify-center gap-2">
                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                Menambahkan...
            </span>
        </button>
    </div>


    @guest
        <p class="text-zinc-500 text-xs mt-3">
            <a href="{{ route('login') }}" class="text-violet-400 hover:underline">Login</a> untuk checkout lebih cepat.
        </p>
    @endguest
</div>
