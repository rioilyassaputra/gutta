<x-admin-layout>
    <x-slot:title>Tambah Produk Baru</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header Actions --}}
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar Produk
                </a>
                <h2 class="text-lg font-black uppercase tracking-wider text-white">Tambah Produk</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-8 bg-zinc-950 p-6 sm:p-8 border-2 border-white/10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Product Name --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
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
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Harga (Rupiah) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0"
                           class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                           placeholder="185000">
                    @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Weight Grams --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Berat (Gram) <span class="text-red-500">*</span></label>
                    <input type="number" name="weight_grams" value="{{ old('weight_grams') }}" required min="1"
                           class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                           placeholder="200">
                    @error('weight_grams') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Status Produk</label>
                    <select name="is_active"
                            class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-aktif</option>
                    </select>
                    @error('is_active') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Deskripsi Produk</label>
                <textarea name="description" rows="4"
                          class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                          placeholder="Tulis spesifikasi bahan, fitting, dll...">{{ old('description') }}</textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Image Uploads --}}
            <div class="border-t border-white/10 pt-6">
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Foto Produk (Bisa multiple, maks 5MB/foto)</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors">
                <p class="text-zinc-500 text-[10px] mt-1">Format didukung: JPEG, PNG, WebP. Foto pertama otomatis jadi foto utama.</p>
                @error('images') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Dynamic Variant Manager using Alpine.js --}}
            <div class="border-t border-white/10 pt-6" x-data="{
                variants: [
                    { size: 'S', sku: '', stock: 10, additional_price: 0 },
                    { size: 'M', sku: '', stock: 15, additional_price: 0 },
                    { size: 'L', sku: '', stock: 15, additional_price: 0 },
                    { size: 'XL', sku: '', stock: 10, additional_price: 0 }
                ],
                addVariant() {
                    this.variants.push({ size: '', sku: '', stock: 0, additional_price: 0 });
                },
                removeVariant(index) {
                    this.variants.splice(index, 1);
                }
            }">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400">Variant Ukuran & Stok <span class="text-red-500">*</span></label>
                    <button type="button" @click="addVariant" class="text-xs font-black uppercase tracking-widest text-violet-400 hover:text-white flex items-center gap-1">
                        + Tambah Variant
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 bg-black/40 p-3 border border-white/5 relative">
                            {{-- Size --}}
                            <div>
                                <label class="block text-[10px] text-zinc-500 uppercase mb-1">Ukuran</label>
                                <input type="text" :name="`variants[${index}][size]`" x-model="variant.size" required
                                       class="w-full bg-black border border-white/10 focus:border-violet-600 text-white rounded-none py-1 px-2 text-xs focus:outline-none"
                                       placeholder="S">
                            </div>

                            {{-- SKU --}}
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] text-zinc-500 uppercase mb-1">SKU (Opsional)</label>
                                <input type="text" :name="`variants[${index}][sku]`" x-model="variant.sku"
                                       class="w-full bg-black border border-white/10 focus:border-violet-600 text-white rounded-none py-1 px-2 text-xs focus:outline-none"
                                       placeholder="Auto-generate jika kosong">
                            </div>

                            {{-- Stock --}}
                            <div>
                                <label class="block text-[10px] text-zinc-500 uppercase mb-1">Stok</label>
                                <input type="number" :name="`variants[${index}][stock]`" x-model="variant.stock" required min="0"
                                       class="w-full bg-black border border-white/10 focus:border-violet-600 text-white rounded-none py-1 px-2 text-xs focus:outline-none text-center">
                            </div>

                            {{-- Additional Price --}}
                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-[10px] text-zinc-500 uppercase mb-1">Tambahan Harga</label>
                                    <input type="number" :name="`variants[${index}][additional_price]`" x-model="variant.additional_price" min="0"
                                           class="w-full bg-black border border-white/10 focus:border-violet-600 text-white rounded-none py-1 px-2 text-xs focus:outline-none">
                                </div>
                                <button type="button" @click="removeVariant(index)" x-show="variants.length > 1"
                                        class="p-2 border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end pt-6 border-t border-white/10">
                <button type="submit" class="btn-primary py-3 px-8 text-sm">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
