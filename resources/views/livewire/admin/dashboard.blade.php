<div class="space-y-8">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        {{-- Stats Card: Orders Today --}}
        <div class="border-2 border-white/10 bg-zinc-950 p-6">
            <span class="text-xs font-black uppercase tracking-widest text-zinc-500">Pesanan Hari Ini</span>
            <p class="text-3xl font-black mt-2 text-white font-mono">{{ $stats['orders_today'] }}</p>
            <p class="text-xs text-zinc-500 mt-1">Total checkout hari ini</p>
        </div>

        {{-- Stats Card: Revenue Today --}}
        <div class="border-2 border-white/10 bg-zinc-950 p-6">
            <span class="text-xs font-black uppercase tracking-widest text-zinc-500">Pendapatan Hari Ini</span>
            <p class="text-3xl font-black mt-2 text-violet-400 font-mono">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</p>
            <p class="text-xs text-zinc-500 mt-1">Dari pesanan yang sudah dibayar</p>
        </div>

        {{-- Stats Card: Need Processing --}}
        <div class="border-2 border-white/10 bg-zinc-950 p-6">
            <span class="text-xs font-black uppercase tracking-widest text-zinc-500">Perlu Diproses</span>
            <p class="text-3xl font-black mt-2 text-yellow-500 font-mono">{{ $stats['need_processing'] }}</p>
            <p class="text-xs text-zinc-500 mt-1">Pesanan dibayar & belum diproses</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Orders Table --}}
        <div class="lg:col-span-2 border-2 border-white/10 bg-zinc-950 p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black uppercase tracking-wider text-white">Pesanan Terbaru</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-black uppercase tracking-widest text-violet-400 hover:text-white border-b-2 border-violet-400 hover:border-white transition-colors">
                    Semua Pesanan
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-zinc-500 font-black uppercase tracking-wider">
                            <th class="pb-3">No. Pesanan</th>
                            <th class="pb-3">Pelanggan</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="py-3 font-mono font-bold text-white">{{ $order->order_number }}</td>
                                <td class="py-3">
                                    <p class="font-bold text-white">{{ $order->user->name }}</p>
                                    <p class="text-zinc-500 text-[10px]">{{ $order->user->email }}</p>
                                </td>
                                <td class="py-3 text-white font-bold">{{ $order->formatted_total }}</td>
                                <td class="py-3">
                                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-none {{ $order->status_color }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-violet-400 hover:text-white transition-colors font-bold uppercase tracking-wider">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-zinc-500">Tidak ada pesanan terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Low Stock Alerts sidebar --}}
        <div class="border-2 border-white/10 bg-zinc-950 p-6 space-y-6">
            <h2 class="text-lg font-black uppercase tracking-wider text-white">Stok Menipis</h2>

            <div class="divide-y divide-white/5 space-y-4 max-h-96 overflow-y-auto pr-2">
                @forelse($stats['low_stock'] as $low)
                    <div class="flex items-center justify-between pt-4 first:pt-0">
                        <div>
                            <h3 class="text-xs font-bold text-white">{{ $low->product->name }}</h3>
                            <p class="text-[10px] text-zinc-500 font-mono mt-0.5">Size: {{ $low->size }} | SKU: {{ $low->sku }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black bg-red-600/20 text-red-500 border border-red-600/40 px-2 py-1">
                                {{ $low->stock }} pcs
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-zinc-500 text-xs text-center py-4">Semua stok varian aman.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Full-width Stock Manager --}}
    <div class="mt-8">
        <livewire:admin.stock-manager />
    </div>
</div>
