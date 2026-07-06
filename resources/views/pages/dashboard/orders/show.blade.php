<x-app-layout>
    <x-slot:title>Detail Pesanan {{ $order->order_number }} — Gutta</x-slot:title>
    <x-slot:metaDescription>Detail pesanan Anda di Gutta Store. Pantau status pembayaran dan pengiriman.</x-slot:metaDescription>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Header --}}
        <div class="mb-10 pb-6 border-b-2 border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Dashboard
                </a>
                <div class="flex items-center gap-4 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-wider font-mono">{{ $order->order_number }}</h1>
                    <span class="text-xs font-black uppercase tracking-wider px-3 py-1 {{ $order->status_color }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <p class="text-zinc-500 text-xs mt-2">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            
            {{-- Payment actions if pending payment --}}
            @if($order->status === 'pending_payment' && $order->payment_token)
                <div class="flex flex-col items-stretch md:items-end gap-2">
                    <button id="pay-button" class="btn-primary py-3 px-8 text-sm">
                        Bayar Sekarang
                    </button>
                    <p class="text-xs text-zinc-500 text-center md:text-right">Selesaikan pembayaran via Midtrans Snap</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Order Items --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="border-2 border-white/10 bg-zinc-950 p-6 sm:p-8">
                    <h2 class="text-lg font-black uppercase tracking-wider text-white mb-6 pb-3 border-b border-white/10">Produk yang Dipesan</h2>
                    
                    <div class="divide-y divide-white/10 space-y-6">
                        @foreach($order->items as $item)
                            <div class="flex gap-6 pt-6 first:pt-0">
                                <div class="w-20 h-20 bg-zinc-900 border border-white/10 overflow-hidden flex-shrink-0">
                                    <img src="{{ $item->variant?->product?->primaryImage?->webp_url ?? asset('images/logo.png') }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0 flex flex-col sm:flex-row justify-between gap-4">
                                    <div>
                                        <h3 class="font-bold text-white text-base truncate">{{ $item->product_name }}</h3>
                                        <p class="text-xs text-zinc-500 uppercase mt-1">Size: {{ $item->variant_detail }}</p>
                                        <p class="text-sm text-zinc-400 mt-2">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="sm:text-right self-end sm:self-center">
                                        <p class="font-black text-white text-base">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Shipping Status & Tracking --}}
                @if($order->status === 'shipped' || $order->status === 'completed')
                    <div class="border-2 border-white/10 bg-zinc-950 p-6 sm:p-8 space-y-4">
                        <h2 class="text-lg font-black uppercase tracking-wider text-white pb-3 border-b border-white/10">Informasi Pengiriman</h2>
                        <div>
                            <p class="text-zinc-500 text-xs font-black uppercase tracking-widest">Nomor Resi / AWB</p>
                            <p class="text-white font-mono font-bold text-lg mt-1">{{ $order->tracking_number }}</p>
                        </div>
                        @if($order->tracking_url)
                            <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer" class="btn-secondary py-2 px-4 text-xs inline-flex items-center gap-2">
                                Lacak Paket via {{ strtoupper($order->courier) }}
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Summary & Address Sidebars --}}
            <div class="space-y-8">
                {{-- Address Info --}}
                <div class="border-2 border-white/10 bg-zinc-950 p-6 sm:p-8 space-y-4">
                    <h2 class="text-lg font-black uppercase tracking-wider text-white pb-3 border-b border-white/10">Alamat Pengiriman</h2>
                    @php
                        $address = $order->address_snapshot;
                    @endphp
                    @if($address)
                        <p class="font-bold text-white text-sm">{{ $address['recipient_name'] }}</p>
                        <p class="text-zinc-400 text-xs mt-1">{{ $address['phone'] }}</p>
                        <p class="text-zinc-400 text-xs mt-2 leading-relaxed">
                            {{ $address['full_address'] }}, {{ $address['district'] }}, {{ $address['city_name'] }}, {{ $address['province_name'] }} {{ $address['postal_code'] }}
                        </p>
                    @endif
                </div>

                {{-- Ringkasan Biaya --}}
                <div class="border-2 border-white/10 bg-zinc-950 p-6 sm:p-8 space-y-6">
                    <h2 class="text-lg font-black uppercase tracking-wider text-white pb-3 border-b border-white/10">Ringkasan Biaya</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-zinc-400">
                            <span>Subtotal Produk</span>
                            <span class="text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-zinc-400">
                            <span>Ongkos Kirim ({{ strtoupper($order->courier) }} {{ $order->courier_service }})</span>
                            <span class="text-white">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base font-black uppercase tracking-wider pt-3 border-t border-dashed border-white/10">
                            <span>Total Pembayaran</span>
                            <span class="text-violet-400">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap Trigger Script --}}
    @if($order->status === 'pending_payment' && $order->payment_token)
        @push('scripts')
            <script>
                document.getElementById('pay-button')?.addEventListener('click', function() {
                    if (typeof snap !== 'undefined') {
                        snap.pay('{{ explode('|', $order->payment_token)[0] }}', {
                            onSuccess: function(result) {
                                window.location.reload();
                            },
                            onPending: function(result) {
                                window.location.reload();
                            },
                            onError: function(result) {
                                alert('Pembayaran gagal, silakan coba lagi.');
                                window.location.reload();
                            },
                            onClose: function() {
                                window.location.reload();
                            }
                        });
                    } else {
                        alert('Sistem pembayaran sedang tidak siap, silakan coba sesaat lagi.');
                    }
                });
            </script>
        @endpush
    @endif
</x-app-layout>
