<x-app-layout>
    <x-slot:title>Pengaturan Profil — Gutta</x-slot:title>
    <x-slot:metaDescription>Kelola informasi profil, alamat email, dan kata sandi akun Anda di Gutta Store.</x-slot:metaDescription>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-10 pb-6 border-b-2 border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-black uppercase tracking-widest text-violet-400">Akun Saya</span>
                <h1 class="text-3xl font-black uppercase tracking-wider mt-1">Pengaturan Profil</h1>
            </div>
            <div class="text-sm font-medium text-zinc-400">
                Selamat datang kembali, <span class="font-bold text-white">{{ Auth::user()->name }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Dashboard Navigation Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-zinc-950 border-2 border-white/10 p-4 space-y-1">
                    <a href="{{ route('dashboard', ['tab' => 'orders']) }}" 
                       class="flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wider transition-colors text-zinc-400 hover:text-white hover:bg-white/5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Pesanan Saya
                    </a>

                    <a href="{{ route('dashboard', ['tab' => 'addresses']) }}" 
                       class="flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wider transition-colors text-zinc-400 hover:text-white hover:bg-white/5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Buku Alamat
                    </a>

                    <a href="{{ route('profile') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wider transition-colors bg-violet-600 text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Pengaturan Profil
                    </a>

                    <div class="border-t border-white/10 my-2"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wider text-red-400 hover:bg-red-950/10 hover:text-red-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            {{-- Profile Content Area --}}
            <div class="lg:col-span-3 space-y-8">
                <div class="border-2 border-white/10 bg-zinc-950 p-6 sm:p-8">
                    <livewire:profile.update-profile-information-form />
                </div>

                <div class="border-2 border-white/10 bg-zinc-950 p-6 sm:p-8">
                    <livewire:profile.update-password-form />
                </div>

                <div class="border-2 border-red-900/30 bg-zinc-950 p-6 sm:p-8">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
