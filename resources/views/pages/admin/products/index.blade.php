<x-admin-layout>
    <x-slot:title>Kelola Produk</x-slot:title>

    <div class="space-y-6">
        {{-- Header Actions --}}
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-lg font-black uppercase tracking-wider text-white">Daftar Produk</h2>
                <p class="text-zinc-500 text-xs mt-1">Total: {{ $products->total() }} produk terdaftar</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn-primary py-2 px-4 text-xs">
                Tambah Produk Baru
            </a>
        </div>

        {{-- Products Table --}}
        <div class="border-2 border-white/10 bg-zinc-950 overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-black text-xs font-black uppercase tracking-wider text-zinc-400">
                        <th class="p-4">Produk</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4">Berat</th>
                        <th class="p-4">Varian & Stok</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($products as $product)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-4 flex items-center gap-4">
                                <div class="w-12 h-12 bg-zinc-900 border border-white/10 overflow-hidden flex-shrink-0">
                                    <img src="{{ $product->primaryImage?->webp_url ?? asset('images/logo.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-white text-sm truncate max-w-xs">{{ $product->name }}</p>
                                    <p class="text-zinc-500 text-[10px] font-mono mt-0.5">{{ $product->slug }}</p>
                                </div>
                            </td>
                            <td class="p-4 text-zinc-300 font-bold">
                                {{ $product->category->name }}
                            </td>
                            <td class="p-4 font-bold text-white">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-zinc-400">
                                {{ $product->weight_grams }}g
                            </td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($product->variants as $variant)
                                        <span class="text-[10px] font-mono bg-zinc-900 border border-white/10 px-1.5 py-0.5 text-zinc-400">
                                            {{ $variant->size }}: <strong class="text-white">{{ $variant->stock }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="text-xs font-bold {{ $product->is_active ? 'text-green-500' : 'text-red-500' }}">
                                    ● {{ $product->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-ghost py-1 px-2.5 text-xs">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger py-1 px-2.5 text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-zinc-500">
                                Belum ada data produk terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div>
            {{ $products->links() }}
        </div>
    </div>
</x-admin-layout>
