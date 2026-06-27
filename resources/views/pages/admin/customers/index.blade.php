<x-admin-layout>
    <x-slot:title>Kelola Pelanggan</x-slot:title>

    <div class="space-y-6">
        {{-- Header Actions --}}
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-lg font-black uppercase tracking-wider text-white">Daftar Pelanggan</h2>
                <p class="text-zinc-500 text-xs mt-1">Total: {{ $customers->total() }} pembeli terdaftar</p>
            </div>
            <a href="{{ route('admin.customers.export') }}" class="btn-secondary py-2 px-4 text-xs">
                Ekspor CSV (.csv)
            </a>
        </div>

        {{-- Customers Table --}}
        <div class="border-2 border-white/10 bg-zinc-950 overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-black text-xs font-black uppercase tracking-wider text-zinc-400">
                        <th class="p-4">Nama Pelanggan</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">WhatsApp</th>
                        <th class="p-4">Total Pesanan</th>
                        <th class="p-4">Total Pengeluaran (Spend)</th>
                        <th class="p-4">Bergabung Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-4 font-bold text-white">
                                {{ $customer->name }}
                            </td>
                            <td class="p-4 text-zinc-400">
                                {{ $customer->email }}
                            </td>
                            <td class="p-4 font-mono text-zinc-300">
                                @if($customer->whatsapp)
                                    <a href="https://wa.me/{{ $customer->whatsapp }}" target="_blank" rel="noopener noreferrer" class="text-violet-400 hover:text-white underline transition-colors">
                                        +{{ $customer->whatsapp }}
                                    </a>
                                @else
                                    <span class="text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="p-4 text-white font-mono font-bold">
                                {{ $customer->orders_count }} kali
                            </td>
                            <td class="p-4 font-bold text-violet-400">
                                Rp {{ number_format($customer->orders_sum_total ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-zinc-500 text-xs">
                                {{ $customer->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-zinc-500">
                                Belum ada pelanggan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div>
            {{ $customers->links() }}
        </div>
    </div>
</x-admin-layout>
