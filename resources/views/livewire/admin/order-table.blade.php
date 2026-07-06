<div class="space-y-6">
    {{-- Search & Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-zinc-950 p-6 border-2 border-white/10">
        {{-- Search Input --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Cari Pesanan</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                       placeholder="No. Pesanan, nama pembeli, email...">
            </div>
        </div>

        {{-- Status Filter --}}
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Filter Status</label>
            <select wire:model.live="statusFilter" 
                    class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors">
                <option value="">Semua Status</option>
                <option value="pending_payment">Menunggu Pembayaran</option>
                <option value="paid">Dibayar</option>
                <option value="processing">Diproses</option>
                <option value="shipped">Dikirim</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>

        {{-- Courier Filter --}}
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Filter Kurir</label>
            <select wire:model.live="courierFilter" 
                    class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors">
                <option value="">Semua Kurir</option>
                <option value="jne">JNE</option>
                <option value="jnt">J&T</option>
                <option value="sicepat">SiCepat</option>
                <option value="pos">POS Indonesia</option>
            </select>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="border-2 border-white/10 bg-zinc-950 overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-white/10 bg-black text-xs font-black uppercase tracking-wider text-zinc-400">
                    <th class="p-4">No. Pesanan</th>
                    <th class="p-4">Pelanggan</th>
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Kurir</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($orders as $order)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="p-4 font-mono font-bold text-white">
                            {{ $order->order_number }}
                        </td>
                        <td class="p-4">
                            <p class="font-bold text-white">{{ $order->user->name }}</p>
                            <p class="text-zinc-500 text-xs mt-0.5">{{ $order->user->email }}</p>
                        </td>
                        <td class="p-4 text-zinc-400 text-xs">
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="p-4 font-bold text-white">
                            {{ $order->formatted_total }}
                        </td>
                        <td class="p-4">
                            <span class="text-xs font-black uppercase tracking-wider px-2 py-0.5 rounded-none {{ $order->status_color }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="p-4">
                            <p class="text-white text-xs uppercase font-bold">{{ $order->courier }}</p>
                            <p class="text-zinc-500 text-xs uppercase">{{ $order->courier_service }}</p>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-ghost py-1 px-3 text-xs">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-zinc-500">
                            Tidak ada data pesanan yang sesuai filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $orders->links() }}
    </div>
</div>
