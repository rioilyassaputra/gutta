<x-app-layout>
    <x-slot:title>Gutta — Apparel & Streetwear Premium</x-slot:title>

    {{-- ── HERO SECTION ─────────────────────────────────────────────── --}}
    <section class="relative min-h-[90vh] flex items-end overflow-hidden border-b-2 border-white/10">
        {{-- Background gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-violet-950/30 via-black to-black"></div>

        {{-- Grid overlay --}}
        <div class="absolute inset-0 gutta-grid opacity-30"></div>

        {{-- Hero content --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 pt-32">
            <div class="max-w-2xl">
                <p class="section-subtitle mb-6 text-violet-400">New Collection 2026</p>
                <h1 class="text-6xl sm:text-8xl font-black uppercase leading-none tracking-tighter font-display mb-8">
                    WEAR<br>
                    <span class="text-violet-600">YOUR</span><br>
                    IDENTITY
                </h1>
                <p class="text-zinc-400 text-lg mb-10 max-w-md">
                    Apparel streetwear premium untuk yang berani tampil berbeda.
                    Anti-design. Intentional rawness.
                </p>
                <div class="flex items-center gap-4 flex-wrap">
                    <a href="{{ route('shop.products.index') }}" class="btn-primary text-base px-8 py-4">
                        Shop Now
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ route('shop.collections.show', 'hoodie') }}" class="btn-secondary text-base px-8 py-4">
                        Explore Hoodie
                    </a>
                </div>
            </div>
        </div>

        {{-- Decorative corner --}}
        <div class="absolute bottom-0 right-0 w-32 h-32 border-t-2 border-l-2 border-violet-600/30"></div>
        <div class="absolute top-0 left-0 w-32 h-32 border-b-2 border-r-2 border-violet-600/30"></div>
    </section>

    {{-- ── CATEGORIES ────────────────────────────────────────────────── --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="flex items-center justify-between mb-12">
            <div>
                <p class="section-subtitle mb-2 text-violet-400">Browse</p>
                <h2 class="section-title">KOLEKSI</h2>
            </div>
            <a href="{{ route('shop.products.index') }}" class="btn-ghost">Lihat Semua →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-white/10">
            @foreach($categories as $category)
                <a href="{{ route('shop.collections.show', $category->slug) }}"
                   class="group bg-black p-8 hover:bg-zinc-950 transition-colors border-0 relative overflow-hidden">
                    <div class="absolute inset-0 bg-violet-600/0 group-hover:bg-violet-600/5 transition-colors duration-300"></div>
                    <p class="text-zinc-500 text-xs uppercase tracking-widest mb-2">Collection</p>
                    <h3 class="text-2xl font-black uppercase tracking-tight group-hover:text-violet-400 transition-colors">
                        {{ $category->name }}
                    </h3>
                    <p class="text-zinc-600 text-sm mt-2">{{ $category->products_count }} produk</p>
                    <div class="absolute bottom-4 right-4 w-6 h-6 border-b-2 border-r-2 border-violet-600/0 group-hover:border-violet-600/60 transition-colors"></div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ── FEATURED PRODUCTS ─────────────────────────────────────────── --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="flex items-center justify-between mb-12">
            <div>
                <p class="section-subtitle mb-2 text-violet-400">Latest Drop</p>
                <h2 class="section-title">PRODUK TERBARU</h2>
            </div>
            <a href="{{ route('shop.products.index') }}" class="btn-ghost">Shop All →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-0 border-2 border-white/10">
            @forelse($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-4 py-20 text-center text-zinc-500">
                    <p class="text-lg">Belum ada produk. <a href="{{ route('admin.products.create') }}" class="text-violet-400 hover:underline">Tambah produk →</a></p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ── MARQUEE BRAND BAR ─────────────────────────────────────────── --}}
    <div class="border-y-2 border-white/10 py-4 overflow-hidden bg-violet-950/20">
        <div class="flex gap-16 whitespace-nowrap animate-[marquee_20s_linear_infinite]" style="animation: marquee 20s linear infinite;">
            @for($i = 0; $i < 6; $i++)
                <span class="text-sm font-black uppercase tracking-[0.3em] text-violet-400/60">GUTTA STORE</span>
                <span class="text-sm font-black uppercase tracking-[0.3em] text-white/10">✦</span>
                <span class="text-sm font-black uppercase tracking-[0.3em] text-zinc-600">STREETWEAR PREMIUM</span>
                <span class="text-sm font-black uppercase tracking-[0.3em] text-white/10">✦</span>
                <span class="text-sm font-black uppercase tracking-[0.3em] text-violet-400/60">BOYOLALI</span>
                <span class="text-sm font-black uppercase tracking-[0.3em] text-white/10">✦</span>
            @endfor
        </div>
    </div>

    <style>
    @keyframes marquee {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }
    </style>

    {{-- ── CTA SECTION ───────────────────────────────────────────────── --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="border-2 border-violet-600/30 p-12 sm:p-20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-950/20 to-transparent"></div>
            <div class="relative z-10 text-center">
                <h2 class="text-5xl sm:text-7xl font-black uppercase leading-none tracking-tighter font-display mb-6">
                    BE<br><span class="text-violet-600">DIFFERENT</span>
                </h2>
                <p class="text-zinc-400 mb-8 max-w-md mx-auto">
                    Daftar sekarang dan dapatkan akses lebih awal ke koleksi terbaru Gutta.
                </p>
                @guest
                    <a href="{{ route('register') }}" class="btn-primary text-base px-10 py-4">
                        Daftar Sekarang — Gratis
                    </a>
                @else
                    <a href="{{ route('shop.products.index') }}" class="btn-primary text-base px-10 py-4">
                        Explore Collection
                    </a>
                @endguest
            </div>
        </div>
    </section>
</x-app-layout>
