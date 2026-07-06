<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black uppercase tracking-wider text-violet-400">Daftar Alamat</h2>
            <p class="text-zinc-500 text-xs mt-1">Maksimal 5 alamat pengiriman.</p>
        </div>
        @if(!$showForm && $addresses->count() < 5)
            <button wire:click="showAddForm" class="btn-primary py-2 px-4 text-xs">
                Tambah Alamat Baru
            </button>
        @endif
    </div>

    @if($showForm)
        <form wire:submit.prevent="saveAddress" class="border-2 border-white/10 bg-zinc-950 p-6 sm:p-8 space-y-6">
            <h3 class="text-lg font-black uppercase tracking-wider text-white">
                {{ $editingId ? 'Edit Alamat' : 'Tambah Alamat Baru' }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Label Alamat --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Label Alamat (misal: Rumah, Kantor)</label>
                    <input type="text" wire:model="label" class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors" placeholder="Rumah">
                    @error('label') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Nama Penerima --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Nama Penerima</label>
                    <input type="text" wire:model="recipient_name" class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors" placeholder="John Doe">
                    @error('recipient_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- No. WhatsApp/Telepon --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">No. Telepon / WhatsApp</label>
                    <input type="text" wire:model="phone" class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors" placeholder="62812xxxxxx" inputmode="numeric" pattern="[0-9]*" x-data x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')">
                    @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Provinsi --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Provinsi</label>
                    <select wire:model.live="province_id" class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors">
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov['province_id'] }}">{{ $prov['province'] }}</option>
                        @endforeach
                    </select>
                    @error('province_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kota / Kabupaten --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Kota / Kabupaten</label>
                    <select wire:model.live="city_id" class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors" {{ empty($cities) ? 'disabled' : '' }}>
                        <option value="">Pilih Kota/Kabupaten</option>
                        @foreach($cities as $ct)
                            <option value="{{ $ct['city_id'] }}">{{ $ct['type'] }} {{ $ct['city_name'] }}</option>
                        @endforeach
                    </select>
                    @error('city_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kecamatan --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Kecamatan</label>
                    <input type="text" wire:model="district" class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors" placeholder="Kecamatan">
                    @error('district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kode Pos --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Kode Pos</label>
                    <input type="text" wire:model="postal_code" class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors" placeholder="xxxxx">
                    @error('postal_code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Alamat Lengkap --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">Alamat Lengkap</label>
                <textarea wire:model="full_address" rows="3" class="w-full bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors" placeholder="Nama Jalan, No. Rumah, RT/RW, Patokan..."></textarea>
                @error('full_address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Jadikan Default --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_default" wire:model="is_default" class="text-violet-600 focus:ring-violet-500 bg-zinc-900 border-white/20">
                <label for="is_default" class="text-sm text-zinc-400 cursor-pointer select-none">Jadikan Alamat Utama</label>
            </div>

            {{-- Actions --}}
            <div class="flex gap-4 pt-4 border-t border-white/10">
                <button type="submit" class="btn-primary">
                    Simpan Alamat
                </button>
                <button type="button" wire:click="$set('showForm', false)" class="btn-secondary">
                    Batal
                </button>
            </div>
        </form>
    @else
        <div class="grid grid-cols-1 gap-6">
            @forelse($addresses as $addr)
                <div class="border-2 p-6 flex flex-col sm:flex-row sm:items-start justify-between gap-6 {{ $addr->is_default ? 'border-violet-600 bg-violet-950/5' : 'border-white/10 bg-zinc-950' }}">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black uppercase tracking-widest bg-zinc-800 px-2 py-0.5 text-zinc-300">
                                {{ $addr->label }}
                            </span>
                            @if($addr->is_default)
                                <span class="text-xs font-black uppercase tracking-widest bg-violet-600 px-2 py-0.5 text-white">
                                    Utama
                                </span>
                            @endif
                        </div>
                        <p class="font-bold text-white text-base">{{ $addr->recipient_name }} <span class="text-zinc-500 font-medium">— {{ $addr->phone }}</span></p>
                        <p class="text-zinc-400 text-sm leading-relaxed max-w-xl">
                            {{ $addr->full_address }}, {{ $addr->district }}, {{ $addr->city_name }}, {{ $addr->province_name }} {{ $addr->postal_code }}
                        </p>
                    </div>

                    <div class="flex sm:flex-col gap-2 flex-wrap items-stretch justify-start">
                        @if(!$addr->is_default)
                            <button wire:click="setDefault({{ $addr->id }})" class="btn-ghost py-1 px-3 text-xs w-full">
                                Set Utama
                            </button>
                        @endif
                        <button wire:click="editAddress({{ $addr->id }})" class="btn-secondary py-1 px-3 text-xs w-full text-center">
                            Edit
                        </button>
                        <button wire:click="deleteAddress({{ $addr->id }})" 
                                onclick="confirm('Apakah Anda yakin ingin menghapus alamat ini?') || event.stopImmediatePropagation()"
                                class="btn-danger py-1 px-3 text-xs w-full">
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="border-2 border-dashed border-white/10 p-12 text-center bg-zinc-950">
                    <p class="text-zinc-500 text-sm mb-4">Kamu belum memiliki alamat pengiriman terdaftar.</p>
                    <button wire:click="showAddForm" class="btn-primary">
                        Tambah Alamat Pertama
                    </button>
                </div>
            @endforelse
        </div>
    @endif
</div>
