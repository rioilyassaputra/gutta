<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black uppercase tracking-wider text-violet-400">Riwayat Pesanan</h2>
            <p class="text-zinc-500 text-xs mt-1">Daftar transaksi belanja Anda di Gutta.</p>
        </div>
    </div>

    {{-- Brutalist Filter Tabs --}}
    <div class="flex flex-wrap gap-2 border-b-2 border-white/10 pb-4">
        <button wire:click="$set('filterStatus', '')" 
                class="px-4 py-2 text-xs font-bold uppercase tracking-wider border-2 transition-all duration-150 {{ $filterStatus === '' ? 'border-violet-600 bg-violet-950/20 text-white' : 'border-white/10 bg-transparent text-zinc-400 hover:text-white hover:border-white/30' }}">
            Semua
        </button>
        <button wire:click="$set('filterStatus', 'pending_payment')" 
                class="px-4 py-2 text-xs font-bold uppercase tracking-wider border-2 transition-all duration-150 {{ $filterStatus === 'pending_payment' ? 'border-yellow-500 bg-yellow-500/10 text-yellow-400' : 'border-white/10 bg-transparent text-zinc-400 hover:text-white hover:border-white/30' }}">
            Menunggu Pembayaran
        </button>
        <button wire:click="$set('filterStatus', 'paid')" 
                class="px-4 py-2 text-xs font-bold uppercase tracking-wider border-2 transition-all duration-150 {{ $filterStatus === 'paid' ? 'border-blue-500 bg-blue-500/10 text-blue-400' : 'border-white/10 bg-transparent text-zinc-400 hover:text-white hover:border-white/30' }}">
            Dibayar
        </button>
        <button wire:click="$set('filterStatus', 'processing')" 
                class="px-4 py-2 text-xs font-bold uppercase tracking-wider border-2 transition-all duration-150 {{ $filterStatus === 'processing' ? 'border-indigo-500 bg-indigo-500/10 text-indigo-400' : 'border-white/10 bg-transparent text-zinc-400 hover:text-white hover:border-white/30' }}">
            Diproses
        </button>
        <button wire:click="$set('filterStatus', 'shipped')" 
                class="px-4 py-2 text-xs font-bold uppercase tracking-wider border-2 transition-all duration-150 {{ $filterStatus === 'shipped' ? 'border-cyan-500 bg-cyan-500/10 text-cyan-400' : 'border-white/10 bg-transparent text-zinc-400 hover:text-white hover:border-white/30' }}">
            Dikirim
        </button>
        <button wire:click="$set('filterStatus', 'completed')" 
                class="px-4 py-2 text-xs font-bold uppercase tracking-wider border-2 transition-all duration-150 {{ $filterStatus === 'completed' ? 'border-green-500 bg-green-500/10 text-green-400' : 'border-white/10 bg-transparent text-zinc-400 hover:text-white hover:border-white/30' }}">
            Selesai
        </button>
        <button wire:click="$set('filterStatus', 'cancelled')" 
                class="px-4 py-2 text-xs font-bold uppercase tracking-wider border-2 transition-all duration-150 {{ $filterStatus === 'cancelled' ? 'border-red-500 bg-red-500/10 text-red-400' : 'border-white/10 bg-transparent text-zinc-400 hover:text-white hover:border-white/30' }}">
            Dibatalkan
        </button>
    </div>

    {{-- Order List Grid --}}
    <div class="space-y-6">
        @forelse($orders as $order)
            <div class="border-2 border-white/10 bg-zinc-950 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:border-white/20 transition-all duration-150">
                <div class="space-y-4 flex-1">
                    {{-- Order Header Info --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-mono font-bold text-white text-base tracking-tight">
                            {{ $order->order_number }}
                        </span>
                        <span class="text-zinc-600 text-xs">|</span>
                        <span class="text-zinc-500 text-xs">
                            {{ $order->created_at->format('d M Y, H:i') }} WIB
                        </span>
                        <span class="text-zinc-600 text-xs">|</span>
                        <span class="text-xs font-black uppercase tracking-wider px-2 py-0.5 rounded-none {{ $order->status_color }}">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    {{-- Main Item Summary --}}
                    <div class="flex gap-4">
                        @if($order->items->isNotEmpty())
                            @php
                                $firstItem = $order->items->first();
                                $productImage = $firstItem->variant?->product?->primaryImage?->webp_url ?? asset('images/logo.png');
                            @endphp
                            <div class="w-16 h-16 bg-zinc-900 border border-white/10 overflow-hidden flex-shrink-0">
                                <img src="{{ $productImage }}" alt="{{ $firstItem->product_name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-white text-sm truncate max-w-sm">{{ $firstItem->product_name }}</p>
                                <p class="text-zinc-500 text-xs mt-0.5 uppercase">Size: {{ $firstItem->variant_detail }}</p>
                                @if($order->items->count() > 1)
                                    <p class="text-zinc-400 text-xs mt-1 font-bold">+{{ $order->items->count() - 1 }} produk lainnya</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Payment / Actions --}}
                <div class="flex flex-col items-end gap-2 text-right">
                    <div>
                        <p class="text-zinc-500 text-xs">Total Pembayaran</p>
                        <p class="text-white font-black text-lg mt-0.5">{{ $order->formatted_total }}</p>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <a href="{{ route('orders.show', $order->order_number) }}" 
                           class="btn-secondary py-2 px-4 text-xs w-full md:w-auto text-center">
                            {{ $order->status === 'pending_payment' ? 'Bayar Sekarang' : 'Detail Pesanan' }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="border-2 border-dashed border-white/10 p-12 text-center bg-zinc-950">
                <p class="text-zinc-500 text-sm">Tidak ada transaksi ditemukan.</p>
                <a href="{{ route('shop.products.index') }}" class="btn-primary mt-4">
                    Belanja Sekarang
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="pt-4">
        {{ $orders->links() }}
    </div>
</div>
