<x-admin-layout>
    <x-slot:title>Edit Produk: {{ $product->name }}</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header Actions --}}
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar Produk
                </a>
                <h2 class="text-lg font-black uppercase tracking-wider text-white">Edit Produk</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-8 bg-zinc-950 p-6 sm:p-8 border-2 border-white/10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Product Name --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                           placeholder="Gutta Signature Tee Black">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" required
                            class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Harga (Rupiah) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', (int) $product->price) }}" required min="0"
                           class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                           placeholder="185000">
                    @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Weight Grams --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Berat (Gram) <span class="text-red-500">*</span></label>
                    <input type="number" name="weight_grams" value="{{ old('weight_grams', $product->weight_grams) }}" required min="1"
                           class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                           placeholder="200">
                    @error('weight_grams') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Status Produk</label>
                    <select name="is_active"
                            class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors">
                        <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $product->is_active) ? '' : 'selected' }}>Non-aktif</option>
                    </select>
                    @error('is_active') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Deskripsi Produk</label>
                <textarea name="description" rows="4"
                          class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                          placeholder="Tulis spesifikasi bahan, fitting, dll...">{{ old('description', $product->description) }}</textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Existing Images --}}
            @if($product->images->isNotEmpty())
                <div class="border-t border-white/10 pt-6">
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-3">Foto Produk Saat Ini</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($product->images as $img)
                            <div class="border border-white/10 bg-zinc-900 aspect-square overflow-hidden relative group">
                                <img src="{{ $img->webp_url }}" alt="Foto" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Upload New Images --}}
            <div class="border-t border-white/10 pt-6">
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Tambah Foto Baru</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors">
                <p class="text-zinc-500 text-[10px] mt-1">Format didukung: JPEG, PNG, WebP. Maksimal 5MB per foto.</p>
                @error('images') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Variant stock links --}}
            <div class="border-t border-white/10 pt-6 space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400">Variant Ukuran & Stok Saat Ini</label>
                    <span class="text-[10px] text-zinc-500">Stok dapat diubah melalui widget Stock Manager.</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($product->variants as $variant)
                        <div class="bg-black/40 p-3 border border-white/5 font-mono text-xs">
                            <p class="font-bold text-white">Size: {{ $variant->size }}</p>
                            <p class="text-zinc-500 mt-1">SKU: {{ $variant->sku }}</p>
                            <p class="text-violet-400 font-bold mt-2">Stok: {{ $variant->stock }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end pt-6 border-t border-white/10">
                <button type="submit" class="btn-primary py-3 px-8 text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
