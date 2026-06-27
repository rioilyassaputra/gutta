<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Gutta Manage' }} — Gutta Store</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-black text-white min-h-screen md:h-screen md:overflow-hidden flex flex-col md:flex-row font-sans antialiased">

    {{-- Admin Sidebar Nav --}}
    <aside class="w-full md:w-64 bg-zinc-950 border-r-2 border-white/10 flex-shrink-0 md:min-h-screen flex flex-col">
        {{-- Brand / Logo --}}
        <div class="h-16 flex items-center justify-between px-6 border-b-2 border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-black uppercase tracking-wider text-white">
                <img src="{{ asset('images/logo.png') }}" alt="Gutta" class="h-6 w-auto">
                <span class="text-xs bg-violet-600 px-2 py-0.5 tracking-widest">MANAGE</span>
            </a>
            
            {{-- Mobile toggle button --}}
            <button class="md:hidden text-white hover:text-violet-400" x-data @click="$dispatch('toggle-admin-menu')">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Navigation links --}}
        <nav class="flex-1 py-6 px-4 space-y-2 hidden md:block" id="admin-nav" x-data @toggle-admin-menu.window="$el.classList.toggle('hidden')">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold uppercase tracking-wider transition-colors border-l-2 {{ Route::is('admin.dashboard') ? 'border-violet-600 bg-violet-950/20 text-white' : 'border-transparent text-zinc-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                Dashboard
            </a>

            <a href="{{ route('admin.products.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold uppercase tracking-wider transition-colors border-l-2 {{ Route::is('admin.products.*') ? 'border-violet-600 bg-violet-950/20 text-white' : 'border-transparent text-zinc-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Produk
            </a>

            <a href="{{ route('admin.categories.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold uppercase tracking-wider transition-colors border-l-2 {{ Route::is('admin.categories.*') ? 'border-violet-600 bg-violet-950/20 text-white' : 'border-transparent text-zinc-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Kategori
            </a>

            <a href="{{ route('admin.orders.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold uppercase tracking-wider transition-colors border-l-2 {{ Route::is('admin.orders.*') ? 'border-violet-600 bg-violet-950/20 text-white' : 'border-transparent text-zinc-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Pesanan
            </a>

            <a href="{{ route('admin.customers.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold uppercase tracking-wider transition-colors border-l-2 {{ Route::is('admin.customers.*') ? 'border-violet-600 bg-violet-950/20 text-white' : 'border-transparent text-zinc-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Pelanggan
            </a>

            <div class="border-t border-white/10 my-4"></div>

            <a href="{{ route('home') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold uppercase tracking-wider transition-colors border-l-2 border-transparent text-zinc-500 hover:text-white hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Toko
            </a>
        </nav>

        {{-- Sidebar Footer / Auth --}}
        <div class="p-4 border-t border-white/10 mt-auto hidden md:block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-white truncate max-w-40">{{ Auth::user()->name }}</p>
                    <span class="text-[10px] text-zinc-500 font-bold uppercase">Administrator</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-bold transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Area --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Top Bar --}}
        <header class="h-16 bg-zinc-950 border-b border-white/10 flex items-center justify-between px-8">
            <h1 class="text-lg font-black uppercase tracking-wider text-white">
                {{ $title ?? 'Dashboard' }}
            </h1>
            <div class="flex items-center gap-4 text-xs font-mono text-zinc-500">
                <span>LOCAL TIME: {{ date('H:i') }} WIB</span>
            </div>
        </header>

        {{-- Content Area --}}
        <main class="flex-1 p-8 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    {{-- Flash Notifications Toast System --}}
    <div x-data="{ notifications: [] }"
         @notify.window="notifications.push({ message: $event.detail.message, type: $event.detail.type, id: Date.now() }); setTimeout(() => notifications.shift(), 4000)"
         class="fixed top-6 right-4 z-[100] flex flex-col gap-2">
        <template x-for="notif in notifications" :key="notif.id">
            <div x-show="true"
                 x-transition:enter="animate-fade-in-up"
                 :class="notif.type === 'success' ? 'bg-green-600 border-green-500' : 'bg-red-600 border-red-500'"
                 class="border-2 text-white px-6 py-3 text-sm font-bold flex items-center gap-3 min-w-64">
                <svg x-show="notif.type === 'success'" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="notif.type !== 'success'" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span x-text="notif.message"></span>
            </div>
        </template>
    </div>

    @livewireScripts
</body>
</html>
