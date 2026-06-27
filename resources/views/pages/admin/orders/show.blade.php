<x-admin-layout>
    <x-slot:title>Detail Pesanan {{ $order->order_number }}</x-slot:title>

    <div class="space-y-8">
        {{-- Navigation & Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
            <div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar Pesanan
                </a>
                <div class="flex items-center gap-4 flex-wrap">
                    <h2 class="text-xl sm:text-2xl font-black font-mono text-white">{{ $order->order_number }}</h2>
                    <span class="text-xs font-black uppercase tracking-wider px-2 py-0.5 rounded-none {{ $order->status_color }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <p class="text-zinc-500 text-xs mt-1">Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
            </div>

            {{-- Fulfill / Actions --}}
            <div class="flex gap-2 self-start sm:self-center">
                {{-- If paid, show mark as processing --}}
                @if($order->status === 'paid')
                    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-primary py-2.5 px-6 text-xs">
                            Proses Pesanan (Ubah ke Diproses)
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Ordered Items Table --}}
                <div class="border-2 border-white/10 bg-zinc-950 p-6 space-y-6">
                    <h3 class="text-base font-black uppercase tracking-wider text-white border-b border-white/10 pb-3">Produk yang Dipesan</h3>

                    <div class="divide-y divide-white/10 space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex gap-4 pt-4 first:pt-0">
                                <div class="w-16 h-16 bg-zinc-900 border border-white/10 overflow-hidden flex-shrink-0">
                                    <img src="{{ $item->variant?->product?->primaryImage?->webp_url ?? asset('images/logo.png') }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0 flex flex-col sm:flex-row justify-between gap-4">
                                    <div>
                                        <h4 class="font-bold text-white text-sm truncate">{{ $item->product_name }}</h4>
                                        <p class="text-[10px] text-zinc-500 uppercase mt-0.5">Size: {{ $item->variant_detail }}</p>
                                        <p class="text-zinc-400 text-xs mt-1">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="sm:text-right self-end sm:self-center">
                                        <p class="font-bold text-white text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Shipping resi input if status is processing --}}
                @if($order->status === 'processing')
                    <div class="border-2 border-white/10 bg-zinc-950 p-6 space-y-4">
                        <h3 class="text-base font-black uppercase tracking-wider text-white border-b border-white/10 pb-3">Kirim Pesanan (Input Nomor Resi)</h3>
                        <form method="POST" action="{{ route('admin.orders.tracking', $order->id) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Nomor Resi / AWB</label>
                                <div class="flex gap-2">
                                    <input type="text" name="tracking_number" required
                                           class="flex-1 bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                                           placeholder="Input resi {{ strtoupper($order->courier) }}">
                                    <button type="submit" class="btn-primary py-2 px-6 text-xs whitespace-nowrap">
                                        Kirim Resi
                                    </button>
                                </div>
                                @error('tracking_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Shipment tracking details --}}
                @if($order->tracking_number)
                    <div class="border-2 border-white/10 bg-zinc-950 p-6 space-y-4">
                        <h3 class="text-base font-black uppercase tracking-wider text-white border-b border-white/10 pb-3">Informasi Pengiriman</h3>
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Nomor Resi / AWB</p>
                            <p class="text-white font-mono font-bold text-base mt-1">{{ $order->tracking_number }}</p>
                        </div>
                        @if($order->tracking_url)
                            <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer" class="btn-secondary py-1.5 px-4 text-xs inline-flex items-center gap-2">
                                Lacak Paket via {{ strtoupper($order->courier) }}
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Buyer Details --}}
                <div class="border-2 border-white/10 bg-zinc-950 p-6 space-y-4">
                    <h3 class="text-base font-black uppercase tracking-wider text-white border-b border-white/10 pb-3">Informasi Pelanggan</h3>
                    <div>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Nama Pembeli</p>
                        <p class="text-white font-bold text-sm mt-0.5">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Email</p>
                        <p class="text-white text-xs mt-0.5">{{ $order->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">WhatsApp</p>
                        <p class="text-white text-xs mt-0.5">
                            @if($order->user->whatsapp)
                                <a href="https://wa.me/{{ $order->user->whatsapp }}" target="_blank" rel="noopener noreferrer" class="text-violet-400 hover:text-white underline transition-colors">
                                    +{{ $order->user->whatsapp }}
                                </a>
                            @else
                                <span class="text-zinc-500">—</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Delivery Address --}}
                <div class="border-2 border-white/10 bg-zinc-950 p-6 space-y-4">
                    <h3 class="text-base font-black uppercase tracking-wider text-white border-b border-white/10 pb-3">Alamat Pengiriman</h3>
                    @php
                        $address = $order->address_snapshot;
                    @endphp
                    @if($address)
                        <p class="font-bold text-white text-xs">{{ $address['recipient_name'] }} <span class="font-medium text-zinc-400">({{ $address['phone'] }})</span></p>
                        <p class="text-[11px] text-zinc-400 mt-2 leading-relaxed">
                            {{ $address['full_address'] }}, {{ $address['district'] }}, {{ $address['city_name'] }}, {{ $address['province_name'] }} {{ $address['postal_code'] }}
                        </p>
                    @endif
                </div>

                {{-- Billing Summary --}}
                <div class="border-2 border-white/10 bg-zinc-950 p-6 space-y-4">
                    <h3 class="text-base font-black uppercase tracking-wider text-white border-b border-white/10 pb-3">Ringkasan Biaya</h3>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between text-zinc-400">
                            <span>Subtotal Produk</span>
                            <span class="text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-zinc-400">
                            <span>Ongkos Kirim ({{ strtoupper($order->courier) }} {{ $order->courier_service }})</span>
                            <span class="text-white">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-black uppercase tracking-wider pt-2.5 border-t border-dashed border-white/10 text-sm">
                            <span>Grand Total</span>
                            <span class="text-violet-400">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
