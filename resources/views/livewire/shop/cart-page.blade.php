<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ showConfirmModal: false }">
    <div class="flex items-center justify-between mb-10">
        <div>
            <p class="section-subtitle mb-1 text-violet-400">Your</p>
            <h1 class="section-title text-4xl">KERANJANG</h1>
        </div>
        @if($cartItems->isNotEmpty())
            <button type="button"
                    @click="showConfirmModal = true"
                    class="btn-danger text-xs">
                Kosongkan Keranjang
            </button>
        @endif
    </div>

    @if($cartItems->isEmpty())
        <div class="border-2 border-white/10 p-20 text-center">
            <div class="text-6xl mb-6">🛒</div>
            <h2 class="text-2xl font-black uppercase mb-4">Keranjang Kosong</h2>
            <p class="text-zinc-500 mb-8">Belum ada produk di keranjangmu.</p>
            <a href="{{ route('shop.products.index') }}" class="btn-primary">Shop Now</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- ── Cart Items ───────────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-0 border-2 border-white/10">
                @foreach($cartItems as $item)
                    <div class="flex items-start gap-4 p-4 border-b border-white/10 last:border-b-0 hover:bg-white/5 transition-colors">
                        {{-- Product Image --}}
                        <a href="{{ route('shop.products.show', $item->variant->product->slug) }}" class="flex-shrink-0">
                            @if($item->variant->product->primaryImage)
                                <img src="{{ $item->variant->product->primaryImage->webp_url }}"
                                     alt="{{ $item->variant->product->name }}"
                                     class="w-20 h-20 object-cover border border-white/10">
                            @else
                                <div class="w-20 h-20 bg-zinc-900 border border-white/10 flex items-center justify-center">
                                    <span class="text-zinc-700 text-xs">?</span>
                                </div>
                            @endif
                        </a>

                        {{-- Product Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('shop.products.show', $item->variant->product->slug) }}"
                               class="font-bold text-sm hover:text-violet-400 transition-colors block truncate">
                                {{ $item->variant->product->name }}
                            </a>
                            <p class="text-zinc-500 text-xs mt-1">Ukuran: <span class="text-zinc-300">{{ $item->variant->size }}</span></p>
                            <p class="text-white font-black mt-2">
                                Rp {{ number_format($item->variant->getFinalPriceAttribute(), 0, ',', '.') }}
                            </p>

                            {{-- Qty Controls --}}
                            <div class="flex items-center gap-4 mt-3">
                                <div class="flex items-center gap-0 border border-zinc-700">
                                    <button wire:click="updateQty({{ $item->id }}, {{ $item->qty - 1 }})"
                                            class="w-8 h-8 flex items-center justify-center hover:bg-white/10 transition-colors text-sm">
                                        −
                                    </button>
                                    <span class="w-10 text-center text-sm font-bold">{{ $item->qty }}</span>
                                    <button wire:click="updateQty({{ $item->id }}, {{ $item->qty + 1 }})"
                                            class="w-8 h-8 flex items-center justify-center hover:bg-white/10 transition-colors text-sm">
                                        +
                                    </button>
                                </div>

                                <button wire:click="removeItem({{ $item->id }})"
                                        class="text-red-400 hover:text-red-300 transition-colors text-xs uppercase tracking-wider">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        {{-- Subtotal --}}
                        <div class="text-right flex-shrink-0">
                            <p class="font-black text-sm">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Order Summary ─────────────────────────────────────── --}}
            <div class="lg:col-span-1">
                <div class="border-2 border-white/10 p-6 sticky top-24">
                    <h2 class="text-sm font-black uppercase tracking-widest mb-6 pb-4 border-b border-white/10">
                        Ringkasan Pesanan
                    </h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-400">Subtotal ({{ $cartItems->sum('qty') }} item)</span>
                            <span class="font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-zinc-500">
                            <span>Ongkos Kirim</span>
                            <span>Dihitung saat checkout</span>
                        </div>
                    </div>

                    <div class="border-t-2 border-white/10 pt-4 mb-6">
                        <div class="flex justify-between">
                            <span class="font-black uppercase tracking-wider text-sm">Total</span>
                            <span class="font-black text-xl text-violet-400">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('checkout.index') }}" class="btn-primary w-full text-center text-sm py-4 justify-center">
                            Lanjut ke Checkout
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    @else
                        <div class="space-y-3">
                            <a href="{{ route('login') }}" class="btn-primary w-full text-center text-sm py-4 justify-center">
                                Login untuk Checkout
                            </a>
                            <p class="text-zinc-500 text-xs text-center">
                                Belum punya akun? <a href="{{ route('register') }}" class="text-violet-400 hover:underline">Daftar</a>
                            </p>
                        </div>
                    @endauth

                    <a href="{{ route('shop.products.index') }}" class="block text-center text-xs text-zinc-500 hover:text-white transition-colors mt-4">
                        ← Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Custom Confirmation Modal --}}
    <div x-show="showConfirmModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="w-full max-w-md bg-zinc-950 border-2 border-white/10 p-6 sm:p-8 space-y-6 text-center transform transition-all"
             @click.outside="showConfirmModal = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="w-16 h-16 bg-red-950/30 border-2 border-red-500 text-red-500 rounded-none flex items-center justify-center mx-auto text-2xl font-bold">
                ⚠️
            </div>
            
            <div class="space-y-2">
                <h3 class="text-lg font-black uppercase tracking-wider text-white">Kosongkan Keranjang?</h3>
                <p class="text-zinc-400 text-sm">Semua produk yang ada di dalam keranjang belanja Anda akan dihapus secara permanen.</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4 pt-2">
                <button @click="showConfirmModal = false" 
                        class="bg-transparent hover:bg-white/5 text-zinc-400 hover:text-white font-bold rounded-none border-2 border-white/10 px-6 py-3 transition-colors text-xs uppercase tracking-wider">
                    Batal
                </button>
                <button @click="showConfirmModal = false; $wire.clearCart()" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold rounded-none border-2 border-red-600 px-6 py-3 transition-colors text-xs uppercase tracking-wider">
                    Ya, Hapus Semua
                </button>
            </div>
        </div>
    </div>
</div>
