<div class="space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- ── LEFT/CENTER: CATEGORY LIST ───────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Search & Header --}}
            <div class="bg-zinc-950 p-6 border-2 border-white/10 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-black uppercase tracking-wider text-white">Daftar Kategori</h2>
                        <p class="text-zinc-500 text-xs mt-1">Kelola kategori produk yang aktif di Gutta Store.</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Cari Kategori</label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors"
                           placeholder="Cari berdasarkan nama atau slug kategori...">
                </div>
            </div>

            {{-- Table --}}
            <div class="border-2 border-white/10 bg-zinc-950 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-white/10 text-zinc-500 font-black uppercase tracking-wider bg-black/40">
                                <th class="p-4">Nama</th>
                                <th class="p-4">Slug</th>
                                <th class="p-4">Deskripsi</th>
                                <th class="p-4 text-center">Jumlah Produk</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-mono">
                            @forelse($categories as $category)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="p-4 font-bold text-white text-sm font-sans">{{ $category->name }}</td>
                                    <td class="p-4 text-zinc-400">{{ $category->slug }}</td>
                                    <td class="p-4 text-zinc-500 max-w-xs truncate font-sans">{{ $category->description ?: '-' }}</td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 text-[11px] font-black rounded-none {{ $category->products_count > 0 ? 'bg-violet-950/40 text-violet-400 border border-violet-800/50' : 'bg-zinc-900 text-zinc-500 border border-zinc-800' }}">
                                            {{ $category->products_count }} produk
                                        </span>
                                    </td>
                                    <td class="p-4 text-right space-x-2 font-sans">
                                        <button wire:click="editCategory({{ $category->id }})" 
                                                class="text-violet-400 hover:text-white transition-colors font-bold uppercase tracking-wider">
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $category->id }})" 
                                                class="text-red-400 hover:text-red-300 transition-colors font-bold uppercase tracking-wider">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-zinc-500 font-sans">Tidak ada kategori ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: ADD/EDIT FORM ─────────────────────────────────── --}}
        <div class="space-y-6">
            <div class="bg-zinc-950 p-6 border-2 border-white/10 space-y-6">
                <div>
                    <h2 class="text-lg font-black uppercase tracking-wider text-white">
                        {{ $isEditing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                    </h2>
                    <p class="text-zinc-500 text-xs mt-1">
                        {{ $isEditing ? 'Modifikasi detail kategori yang sudah ada.' : 'Buat kategori baru untuk produk Anda.' }}
                    </p>
                </div>

                <form wire:submit.prevent="saveCategory" class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Nama Kategori</label>
                        <input type="text" wire:model.live="name" 
                               class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Outerwear">
                        @error('name')
                            <span class="text-red-500 text-[11px] mt-1 block font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Slug (URL)</label>
                        <input type="text" wire:model="slug" 
                               class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors @error('slug') border-red-500 @enderror"
                               placeholder="Contoh: outerwear">
                        <span class="text-[10px] text-zinc-500 mt-1 block">Slug akan menentukan URL kategori ini (contoh: <code>/collections/outerwear</code>).</span>
                        @error('slug')
                            <span class="text-red-500 text-[11px] mt-1 block font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Deskripsi (Opsional)</label>
                        <textarea wire:model="description" rows="3" 
                                  class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors @error('description') border-red-500 @enderror"
                                  placeholder="Tulis deskripsi singkat tentang kategori ini..."></textarea>
                        @error('description')
                            <span class="text-red-500 text-[11px] mt-1 block font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" 
                                class="flex-1 bg-violet-700 hover:bg-violet-600 text-white font-bold uppercase tracking-wider text-xs py-3 border-2 border-violet-700 hover:border-violet-600 transition-colors">
                            {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                        </button>
                        
                        @if($isEditing)
                            <button type="button" wire:click="cancelEdit" 
                                    class="bg-transparent hover:bg-white/5 text-zinc-400 hover:text-white font-bold uppercase tracking-wider text-xs py-3 px-4 border-2 border-white/10 transition-colors">
                                Batal
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- ── DELETE CONFIRMATION MODAL ──────────────────────────────── --}}
    @if($confirmingDeleteId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div class="bg-zinc-950 border-2 border-red-600/40 max-w-md w-full p-6 space-y-6">
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-red-600/10 border border-red-600/30 text-red-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-wider text-white">Hapus Kategori?</h3>
                        <p class="text-zinc-400 text-xs mt-2">
                            Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="button" wire:click="cancelDelete" 
                            class="bg-transparent hover:bg-white/5 text-zinc-400 hover:text-white font-bold uppercase tracking-wider text-xs py-2 px-4 border-2 border-white/10 transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="deleteCategory" 
                            class="bg-red-600 hover:bg-red-500 text-white font-bold uppercase tracking-wider text-xs py-2 px-4 border-2 border-red-600 hover:border-red-500 transition-colors">
                        Hapus Permanen
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
