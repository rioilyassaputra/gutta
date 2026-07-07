<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'Gutta — Apparel & Streetwear Premium. Ekspresikan identitasmu.' }}">
    <meta property="og:title" content="{{ isset($title) ? $title . ' — Gutta' : 'Gutta Store' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Gutta — Apparel & Streetwear Premium.' }}">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta name="theme-color" content="#000000">
    <title>{{ isset($title) ? $title . ' — Gutta' : 'Gutta Store' }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-black text-white min-h-screen flex flex-col gutta-grid">

    {{-- ── HEADER ────────────────────────────────────────────────── --}}
    <header class="sticky top-0 z-50 bg-black border-b-2 border-white/10 backdrop-blur-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex-shrink-0 hover:opacity-80 transition-opacity">
                    <img src="{{ asset('images/logo.png') }}" alt="Gutta" class="h-8 w-auto">
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('shop.products.index') }}" class="nav-link">Shop</a>
                    @if($globalCategories->count() <= 4)
                        @foreach($globalCategories as $cat)
                            <a href="{{ route('shop.collections.show', $cat->slug) }}" class="nav-link">{{ $cat->name }}</a>
                        @endforeach
                    @else
                        @foreach($globalCategories->take(3) as $cat)
                            <a href="{{ route('shop.collections.show', $cat->slug) }}" class="nav-link">{{ $cat->name }}</a>
                        @endforeach
                        
                        {{-- Dropdown for more categories --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false">
                            <button @click="open = !open" class="nav-link flex items-center gap-1 py-4 focus:outline-none">
                                More
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 top-full mt-0 w-48 bg-zinc-950 border-2 border-white/10 py-2 z-50 shadow-2xl">
                                @foreach($globalCategories->slice(3) as $cat)
                                    <a href="{{ route('shop.collections.show', $cat->slug) }}" 
                                       class="block px-4 py-2 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white hover:bg-white/10 transition-colors">
                                        {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-4">
                    {{-- Cart Badge --}}
                    <a href="{{ route('cart.index') }}" class="relative flex items-center gap-2 hover:text-violet-400 transition-colors" id="cart-link">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <livewire:components.cart-count />
                    </a>

                    {{-- Auth --}}
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider hover:text-violet-400 transition-colors">
                                <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 top-full mt-2 w-48 bg-zinc-950 border-2 border-white/10 py-2 z-50">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-white/10 transition-colors">Dashboard</a>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-violet-400 hover:bg-white/10 transition-colors">Admin Panel</a>
                                @endif
                                <div class="border-t border-white/10 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/10 transition-colors">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="nav-link text-xs">Login</a>
                        <a href="{{ route('register') }}" class="btn-primary text-xs py-2 px-4">Daftar</a>
                    @endauth

                    {{-- Mobile Menu Button --}}
                    <button class="md:hidden" x-data @click="$dispatch('toggle-mobile-menu')" id="mobile-menu-btn">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        {{-- Mobile Nav --}}
        <div class="md:hidden border-t border-white/10 hidden" id="mobile-nav"
             x-data @toggle-mobile-menu.window="$el.classList.toggle('hidden')">
            <div class="px-4 py-4 flex flex-col gap-4">
                <a href="{{ route('shop.products.index') }}" class="text-sm font-bold uppercase tracking-wider">Shop All</a>
                @foreach($globalCategories as $cat)
                    <a href="{{ route('shop.collections.show', $cat->slug) }}" class="text-sm font-bold uppercase tracking-wider">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>
    </header>

    {{-- ── FLASH NOTIFICATIONS ──────────────────────────────────── --}}
    @if(session('success'))
        <div class="fixed top-20 right-4 z-[100] animate-fade-in-up" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <div class="bg-green-600 border-2 border-green-500 text-white px-6 py-3 text-sm font-bold flex items-center gap-3">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- ── FOOTER ──────────────────────────────────────────────── --}}
    <footer class="border-t-2 border-white/10 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                {{-- Brand --}}
                <div>
                    <img src="{{ asset('images/logo.png') }}" alt="Gutta" class="h-10 w-auto mb-4">
                    <p class="text-zinc-500 text-sm leading-relaxed">
                        Apparel & Streetwear Premium.<br>
                        Dibuat untuk yang berani tampil beda.
                    </p>
                </div>

                {{-- Shop --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-4">Shop</h3>
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('shop.products.index') }}" class="text-sm text-zinc-300 hover:text-white transition-colors">Semua Produk</a>
                        @foreach($globalCategories as $cat)
                            <a href="{{ route('shop.collections.show', $cat->slug) }}" class="text-sm text-zinc-300 hover:text-white transition-colors">{{ $cat->name }}</a>
                        @endforeach
                    </div>
                </div>

                {{-- Info --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-4">Info</h3>
                    <div class="flex flex-col gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm text-zinc-300 hover:text-white transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-zinc-300 hover:text-white transition-colors">Login</a>
                            <a href="{{ route('register') }}" class="text-sm text-zinc-300 hover:text-white transition-colors">Daftar</a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="divider-gutta mt-12 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-zinc-600 text-xs">© {{ date('Y') }} Gutta Store. All rights reserved.</p>
                <p class="text-zinc-600 text-xs">Boyolali, Jawa Tengah — Indonesia</p>
            </div>
        </div>
    </footer>

    {{-- Livewire notification listener --}}
    <div x-data="{ notifications: [] }"
         @notify.window="notifications.push({ message: $event.detail.message, type: $event.detail.type, id: Date.now() }); setTimeout(() => notifications.shift(), 4000)"
         class="fixed top-20 right-4 z-[100] flex flex-col gap-2">
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
    @stack('scripts')
</body>
</html>
