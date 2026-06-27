<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
     x-data="{
        initSnap() {
            window.addEventListener('open-snap', event => {
                const token = event.detail.token;
                const orderId = event.detail.orderId;
                if (typeof snap !== 'undefined') {
                    snap.pay(token, {
                        onSuccess: function(result) {
                            window.location.href = '/orders/' + orderId;
                        },
                        onPending: function(result) {
                            window.location.href = '/orders/' + orderId;
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal, silakan coba lagi.');
                            window.location.href = '/orders/' + orderId;
                        },
                        onClose: function() {
                            window.location.href = '/orders/' + orderId;
                        }
                    });
                } else {
                    alert('Sistem pembayaran sedang tidak siap, silakan coba sesaat lagi.');
                }
            });
        }
     }"
     x-init="initSnap()">

    {{-- Progress Steps Bar --}}
    <div class="mb-12 border-b-2 border-white/10 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs font-black uppercase tracking-widest text-violet-400">Secure Checkout</span>
            <h1 class="text-3xl font-black uppercase tracking-wider mt-1">Selesaikan Pesanan</h1>
        </div>
        <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-wider">
            <span class="{{ $step >= 1 ? 'text-white' : 'text-zinc-600' }}">01. Alamat</span>
            <span class="text-zinc-600">/</span>
            <span class="{{ $step >= 2 ? 'text-white' : 'text-zinc-600' }}">02. Pengiriman</span>
            <span class="text-zinc-600">/</span>
            <span class="{{ $step >= 3 ? 'text-white' : 'text-zinc-600' }}">03. Pembayaran</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Main Form / Step Steps --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- STEP 1: ADDRESS BOOK SELECTION --}}
            @if($step === 1)
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-black uppercase tracking-wider text-violet-400">Pilih Alamat Pengiriman</h2>
                        <a href="{{ route('dashboard') }}?tab=addresses" class="text-xs font-black uppercase tracking-widest text-white border-b-2 border-white hover:text-violet-400 hover:border-violet-400 transition-colors">
                            Kelola Alamat
                        </a>
                    </div>

                    @if($addresses->isEmpty())
                        <div class="border-2 border-dashed border-white/10 p-8 text-center bg-zinc-950">
                            <p class="text-zinc-400 text-sm mb-4">Kamu belum memiliki alamat pengiriman terdaftar.</p>
                            <a href="{{ route('dashboard') }}?tab=addresses" class="btn-primary">
                                Tambah Alamat Baru
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($addresses as $addr)
                                <label class="block border-2 cursor-pointer transition-all duration-150 p-6 {{ $selectedAddressId === $addr->id ? 'border-violet-600 bg-violet-950/20' : 'border-white/10 bg-zinc-950 hover:border-white/30' }}">
                                    <div class="flex items-start gap-4">
                                        <input type="radio" wire:model.live="selectedAddressId" value="{{ $addr->id }}" class="mt-1 text-violet-600 focus:ring-violet-500 bg-zinc-900 border-white/20">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-xs font-black uppercase tracking-widest bg-zinc-800 px-2 py-0.5 text-zinc-300">
                                                    {{ $addr->label }}
                                                </span>
                                                @if($addr->is_default)
                                                    <span class="text-xs font-black uppercase tracking-widest bg-violet-600 px-2 py-0.5 text-white">
                                                        Utama
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="font-bold text-white text-base">{{ $addr->recipient_name }}</p>
                                            <p class="text-zinc-400 text-sm mt-1">{{ $addr->phone }}</p>
                                            <p class="text-zinc-400 text-sm mt-2 leading-relaxed">
                                                {{ $addr->full_address }}, {{ $addr->district }}, {{ $addr->city_name }}, {{ $addr->province_name }} {{ $addr->postal_code }}
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end pt-4">
                            <button wire:click="goToShipping" class="btn-primary" {{ !$selectedAddressId ? 'disabled' : '' }}>
                                Lanjutkan ke Pengiriman
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- STEP 2: SHIPPING SELECTION --}}
            @if($step === 2)
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-black uppercase tracking-wider text-violet-400">Pilih Metode Pengiriman</h2>
                        <button wire:click="backToStep(1)" class="text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali ke Alamat
                        </button>
                    </div>

                    @if($loadingShipping)
                        <div class="space-y-4">
                            <div class="h-20 bg-zinc-900 animate-pulse border border-white/10"></div>
                            <div class="h-20 bg-zinc-900 animate-pulse border border-white/10"></div>
                            <div class="h-20 bg-zinc-900 animate-pulse border border-white/10"></div>
                        </div>
                    @elseif(empty($shippingOptions))
                        <div class="border-2 border-white/10 p-8 text-center bg-zinc-950">
                            <p class="text-red-400 text-sm">Tidak ada opsi pengiriman yang tersedia untuk wilayah Anda. Hubungi Admin.</p>
                            <button wire:click="loadShippingOptions" class="btn-secondary mt-4">Coba Lagi</button>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($shippingOptions as $opt)
                                @php
                                    $isSelected = ($selectedCourier === $opt['courier'] && $selectedCourierService === $opt['service']);
                                @endphp
                                <button type="button" 
                                        wire:click="selectShipping('{{ $opt['courier'] }}', '{{ $opt['service'] }}', {{ $opt['cost'] }}, '{{ $opt['courier_name'] }}')" 
                                        class="block w-full text-left border-2 cursor-pointer transition-all duration-150 p-6 {{ $isSelected ? 'border-violet-600 bg-violet-950/20' : 'border-white/10 bg-zinc-950 hover:border-white/30' }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-black uppercase tracking-wider text-violet-400">
                                                    {{ $opt['courier_name'] }}
                                                </span>
                                                <span class="text-xs text-zinc-500">—</span>
                                                <span class="text-sm font-bold text-white uppercase">{{ $opt['service'] }}</span>
                                            </div>
                                            <p class="text-zinc-400 text-xs">{{ $opt['description'] }}</p>
                                            <p class="text-zinc-500 text-xs mt-1">Estimasi pengiriman: {{ $opt['etd'] }} hari</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-black text-white text-base">Rp {{ number_format($opt['cost'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <div class="flex justify-end pt-4">
                            <button wire:click="goToConfirmation" class="btn-primary" {{ !$selectedCourier ? 'disabled' : '' }}>
                                Lanjutkan ke Pembayaran
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- STEP 3: CONFIRMATION --}}
            @if($step === 3)
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-black uppercase tracking-wider text-violet-400">Konfirmasi Pemesanan</h2>
                        <button wire:click="backToStep(2)" class="text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali ke Pengiriman
                        </button>
                    </div>

                    {{-- Review Details --}}
                    <div class="space-y-6 border-2 border-white/10 bg-zinc-950 p-6 sm:p-8">
                        {{-- Address Summary --}}
                        <div class="pb-6 border-b border-white/10">
                            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Alamat Pengiriman</h3>
                            @php
                                $addr = \App\Models\Address::find($selectedAddressId);
                            @endphp
                            @if($addr)
                                <p class="font-bold text-white">{{ $addr->recipient_name }} <span class="font-medium text-zinc-400">({{ $addr->phone }})</span></p>
                                <p class="text-zinc-400 text-sm mt-1 leading-relaxed">
                                    {{ $addr->full_address }}, {{ $addr->district }}, {{ $addr->city_name }}, {{ $addr->province_name }} {{ $addr->postal_code }}
                                </p>
                            @endif
                        </div>

                        {{-- Courier Summary --}}
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Metode Pengiriman</h3>
                            <p class="font-bold text-white uppercase">{{ $selectedCourierName }} — {{ $selectedCourierService }}</p>
                            <p class="text-zinc-400 text-sm mt-1">Biaya Ongkir: Rp {{ number_format($selectedShippingCost, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button wire:click="createOrder" 
                                wire:loading.attr="disabled"
                                class="btn-primary w-full sm:w-auto text-base py-4 px-8">
                            <span wire:loading.remove>Bayar Sekarang via Midtrans</span>
                            <span wire:loading>Memproses Transaksi...</span>
                        </button>
                    </div>
                </div>
            @endif

        </div>

        {{-- Order Summary Sidebar (Right Side) --}}
        <div class="space-y-8">
            <div class="border-2 border-white/10 bg-zinc-950 p-6 sm:p-8 space-y-6 sticky top-24">
                <h2 class="text-lg font-black uppercase tracking-wider text-white pb-4 border-b border-white/10">Ringkasan Pesanan</h2>

                {{-- Item List --}}
                <div class="divide-y divide-white/5 max-h-80 overflow-y-auto pr-2 space-y-4">
                    @foreach($cartItems as $item)
                        <div class="flex gap-4 pt-4 first:pt-0">
                            <div class="w-16 h-16 bg-zinc-900 border border-white/10 overflow-hidden flex-shrink-0 relative">
                                <img src="{{ $item->variant->product->primaryImage?->webp_url ?? asset('images/logo.png') }}" alt="{{ $item->variant->product->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-white truncate">{{ $item->variant->product->name }}</h3>
                                <p class="text-xs text-zinc-500 uppercase mt-0.5">Size: {{ $item->variant->size }}</p>
                                <p class="text-xs text-zinc-400 mt-1">{{ $item->qty }} x Rp {{ number_format($item->variant->getFinalPriceAttribute(), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Prices calculation --}}
                <div class="space-y-3 pt-6 border-t border-white/10 text-sm">
                    <div class="flex justify-between text-zinc-400">
                        <span>Subtotal</span>
                        <span class="text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($selectedShippingCost > 0)
                        <div class="flex justify-between text-zinc-400">
                            <span>Ongkos Kirim ({{ strtoupper($selectedCourier ?? '') }})</span>
                            <span class="text-white">Rp {{ number_format($selectedShippingCost, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-black uppercase tracking-wider pt-3 border-t border-dashed border-white/10">
                        <span>Total Bayar</span>
                        <span class="text-violet-400">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
