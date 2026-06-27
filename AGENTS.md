# AGENTS.md — Gutta E-Commerce Webstore

> Dokumen ini adalah **instruksi wajib** untuk coding agent (Claude Code, Cursor, Copilot, dll) yang mengerjakan project ini.
> Baca seluruh file ini sebelum menulis satu baris kode pun. Ikuti semua aturan di sini tanpa pengecualian.

---

## 0. Konteks Project

Kamu sedang membangun **Gutta E-Commerce Webstore** — platform penjualan apparel/streetwear independen.

| Item | Detail |
| :--- | :--- |
| **Framework** | Laravel 13, PHP 8.3 |
| **Frontend** | Livewire 3, Alpine.js, Tailwind CSS v3, Vite |
| **Database** | MySQL 8.0 |
| **Cache & Queue** | Redis |
| **Payment** | Midtrans (Snap) |
| **Ongkir API** | RajaOngkir |
| **Image Processing** | Intervention Image v3 |
| **PRD Referensi** | `PRD_Gutta_Ecommerce_v2.md` — baca ini untuk detail fitur lengkap |

---

## 1. Aturan Umum (Wajib Diikuti)

### 1.1 Sebelum Mulai Task Apapun

- [ ] Baca file `PRD_Gutta_Ecommerce_v2.md` untuk memahami konteks fitur yang sedang dikerjakan
- [ ] Cek apakah migration yang relevan sudah ada. Jangan buat tabel duplikat
- [ ] Pastikan kamu tahu apakah task ini menyentuh route publik (buyer) atau admin — keduanya punya middleware berbeda
- [ ] Jangan install package baru tanpa konfirmasi. Tanya dulu jika perlu dependency tambahan

### 1.2 Hal yang DILARANG Keras

```
❌ Jangan gunakan DB::statement() dengan string interpolasi (SQL injection risk)
❌ Jangan gunakan {!! $var !!} di Blade untuk user-generated content (XSS risk)
❌ Jangan hardcode API key, password, atau credential apapun di kode
❌ Jangan gunakan Model::create($request->all()) tanpa $fillable
❌ Jangan buat logika bisnis di dalam Controller — taruh di Service class
❌ Jangan panggil API eksternal (RajaOngkir, Midtrans) langsung dari Livewire component
❌ Jangan expose API key ke JavaScript / frontend
❌ Jangan skip validasi server-side karena "sudah divalidasi di frontend"
❌ Jangan gunakan N+1 query — selalu eager load relasi dengan with()
❌ Jangan commit file .env ke Git
```

### 1.3 Hal yang WAJIB Selalu Dilakukan

```
✅ Semua logika bisnis kompleks → taruh di app/Services/
✅ Semua validasi form → gunakan FormRequest class di app/Http/Requests/
✅ Semua interaksi DB → gunakan Eloquent ORM dengan parameter binding
✅ Semua output di Blade → gunakan {{ $var }} bukan {!! !!}
✅ Semua form POST/PUT/DELETE → sertakan @csrf
✅ Setelah buat fitur baru → pastikan route sudah dilindungi middleware yang tepat
✅ Setiap upload gambar → konversi ke WebP via Intervention Image
✅ Setiap email → kirim via Queue, bukan Mail::send() langsung
✅ Setiap Model → definisikan $fillable, $casts, dan relasi dengan lengkap
```

---

## 2. Setup Lokal (Langkah Berurutan)

### 2.1 Prerequisite

Pastikan sudah terinstall di mesin lokal:
- PHP 8.3 dengan ekstensi: `pdo_mysql`, `redis`, `gd`, `imagick`, `zip`, `bcmath`
- MySQL 8.0
- Redis (jalankan `redis-server`)
- Node.js 20+ dan npm
- Composer 2.x

### 2.2 Instalasi Project

```bash
# 1. Clone repo
git clone <repo-url> gutta-store
cd gutta-store

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Isi .env (lihat Section 2.3)
# Edit .env sesuai konfigurasi lokal kamu

# 6. Jalankan migration SESUAI URUTAN
php artisan migrate

# 7. Jalankan seeder (data awal)
php artisan db:seed

# 8. Build frontend assets
npm run dev

# 9. Buat symlink storage
php artisan storage:link

# 10. Jalankan queue worker (terminal terpisah)
php artisan queue:work --queue=default,emails

# 11. Jalankan development server
php artisan serve
```

### 2.3 Konfigurasi `.env` Minimum untuk Development

```env
APP_NAME="Gutta Store"
APP_ENV=local
APP_KEY=         # auto-generated dari step 4
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gutta_store
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log   # Di dev, email masuk ke storage/logs/laravel.log

# Midtrans Sandbox
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx
MIDTRANS_IS_PRODUCTION=false

# RajaOngkir
RAJAONGKIR_API_KEY=your-key-here
RAJAONGKIR_ORIGIN_CITY_ID=501   # Ganti dengan ID kota gudang Gutta
RAJAONGKIR_COURIERS=jne,jnt,sicepat,pos
```

---

## 3. Struktur Direktori & Konvensi

### 3.1 Peta Direktori Lengkap

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php        # Catalog & detail produk (publik)
│   │   ├── CartController.php           # Halaman cart (auth)
│   │   ├── CheckoutController.php       # Flow checkout (auth)
│   │   ├── OrderController.php          # Dashboard pesanan buyer (auth)
│   │   ├── WebhookController.php        # Endpoint webhook Midtrans (public, verified)
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── ProductController.php
│   │       ├── OrderController.php
│   │       └── CustomerController.php
│   ├── Middleware/
│   │   ├── EnsureUserIsAdmin.php        # Cek role = admin
│   │   └── ForceHttps.php              # Redirect ke HTTPS di production
│   └── Requests/
│       ├── RegisterRequest.php
│       ├── LoginRequest.php
│       ├── AddressRequest.php
│       ├── CheckoutRequest.php
│       └── Admin/
│           ├── ProductRequest.php
│           └── UpdateTrackingRequest.php
├── Livewire/
│   ├── Shop/
│   │   ├── ProductFilter.php            # Filter & sorting katalog
│   │   ├── AddToCart.php               # Tombol add to cart di product detail
│   │   └── CartPage.php                # Halaman keranjang belanja
│   ├── Checkout/
│   │   ├── CheckoutWizard.php          # Orchestrator 3-step checkout
│   │   ├── AddressStep.php
│   │   ├── ShippingStep.php
│   │   └── ConfirmationStep.php
│   ├── Dashboard/
│   │   ├── OrderList.php               # Riwayat pesanan buyer
│   │   └── AddressBook.php             # Manajemen alamat
│   └── Admin/
│       ├── OrderTable.php
│       └── StockManager.php
├── Models/
│   ├── User.php
│   ├── Address.php
│   ├── Category.php
│   ├── Product.php
│   ├── ProductImage.php
│   ├── ProductVariant.php
│   ├── CartItem.php
│   ├── Order.php
│   └── OrderItem.php
├── Services/
│   ├── RajaOngkirService.php
│   ├── PaymentService.php              # Interface abstraksi
│   ├── MidtransPaymentService.php      # Implementasi Midtrans
│   ├── OrderService.php               # Logika create order, update status
│   ├── CartService.php                # Logika cart (merge guest, validasi stok)
│   └── ImageService.php              # Konversi WebP, resize
├── Jobs/
│   ├── SendOrderConfirmationEmail.php
│   ├── SendShippingNotification.php
│   └── SendEmailVerification.php
└── Observers/
    └── OrderObserver.php              # Auto-generate order_number

resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php              # Layout utama (nav, footer)
    │   └── admin.blade.php            # Layout admin
    ├── components/
    │   ├── product-card.blade.php
    │   ├── cart-badge.blade.php
    │   ├── status-badge.blade.php
    │   └── skeleton-loader.blade.php
    ├── livewire/                      # Auto-generated oleh Livewire
    ├── pages/
    │   ├── shop/
    │   │   ├── index.blade.php        # Listing produk
    │   │   └── show.blade.php         # Detail produk
    │   ├── cart/
    │   │   └── index.blade.php
    │   ├── checkout/
    │   │   └── index.blade.php
    │   ├── dashboard/
    │   │   ├── index.blade.php
    │   │   └── orders/show.blade.php
    │   └── admin/
    │       ├── dashboard.blade.php
    │       ├── orders/
    │       ├── products/
    │       └── customers/
    └── emails/
        ├── order-confirmation.blade.php
        └── shipping-notification.blade.php

database/
└── migrations/                        # Urutan migration penting! Lihat Section 4
```

### 3.2 Konvensi Penamaan

| Komponen | Format | Contoh |
| :--- | :--- | :--- |
| Model | PascalCase singular | `ProductVariant` |
| Migration | snake_case dengan timestamp | `2024_01_01_000001_create_users_table` |
| Controller | PascalCase + Controller | `ProductController` |
| Livewire | PascalCase | `AddToCart`, `CheckoutWizard` |
| Service | PascalCase + Service | `OrderService` |
| FormRequest | PascalCase + Request | `CheckoutRequest` |
| Job | PascalCase deskriptif | `SendOrderConfirmationEmail` |
| Route name | kebab-case dengan dot | `shop.products.show`, `admin.orders.index` |
| Blade view | kebab-case | `product-card.blade.php` |
| CSS class | Tailwind utility — ikuti design system di Section 6 | |

---

## 4. Urutan Migration (KRITIS — Jangan Diubah)

Jalankan migration dalam urutan ini untuk menghindari FK constraint error:

```
1.  create_users_table
2.  create_password_reset_tokens_table
3.  create_categories_table
4.  create_products_table
5.  create_product_images_table
6.  create_product_variants_table
7.  create_addresses_table
8.  create_cart_items_table
9.  create_orders_table
10. create_order_items_table
11. create_personal_access_tokens_table   (Sanctum)
12. add_indexes_to_all_tables             (Index migration terpisah — jalankan terakhir)
```

> **Penting:** Migration index (no. 12) harus dijalankan setelah semua tabel terbuat.
> Isi index wajib: `users.email`, `orders.user_id`, `orders.order_number`, `orders.status`, `products.slug`, `cart_items.user_id`, `product_variants.product_id`, `orders.tracking_number`

---

## 5. Routing & Middleware

### 5.1 Grup Route

```php
// routes/web.php

// ── PUBLIK (tanpa auth) ──────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('shop.products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('shop.products.show');
Route::get('/collections/{category:slug}', [ProductController::class, 'byCategory'])->name('shop.collections.show');

// ── AUTH REQUIRED ────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/addresses', AddressController::class)->except(['show']);
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
});

// ── ADMIN ────────────────────────────────────────────────
Route::prefix('gutta-manage')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', Admin\ProductController::class);
    Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::post('orders/{order}/tracking', [Admin\OrderController::class, 'updateTracking'])->name('orders.tracking');
    Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/export', [Admin\CustomerController::class, 'export'])->name('customers.export');
});

// ── WEBHOOK (publik tapi diverifikasi di Controller) ──────
Route::post('/webhooks/midtrans', [WebhookController::class, 'midtrans'])
    ->name('webhooks.midtrans')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

### 5.2 Registrasi Middleware

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        'force.https' => \App\Http\Middleware\ForceHttps::class,
    ]);
    $middleware->throttleWithRedis(); // Gunakan Redis untuk throttle
})
```

### 5.3 Contoh Implementasi Middleware Admin

```php
// app/Http/Middleware/EnsureUserIsAdmin.php
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized.');
    }
    return $next($request);
}
```

---

## 6. Design System & Tailwind

### 6.1 Palet Warna Gutta

Tambahkan ke `tailwind.config.js`:

```js
theme: {
  extend: {
    colors: {
      gutta: {
        black:  '#000000',
        purple: '#6d28d9',
        dark:   '#4c1d95',
        light:  '#ede9fe',
        white:  '#ffffff',
      }
    },
    fontFamily: {
      sans: ['Inter', 'Space Grotesk', 'sans-serif'],
    }
  }
}
```

### 6.2 Komponen UI yang Harus Konsisten

```
Tombol Primary CTA:
  class="bg-gutta-purple hover:bg-gutta-black text-white font-bold
         rounded-none border-2 border-gutta-purple hover:border-gutta-black
         px-6 py-3 transition-colors duration-150"

Tombol Secondary / Outline:
  class="bg-transparent hover:bg-gutta-black text-gutta-purple hover:text-white
         font-bold rounded-none border-2 border-gutta-purple
         px-6 py-3 transition-colors duration-150"

Card Produk:
  class="border-2 border-black group cursor-pointer overflow-hidden"

Badge Status:
  pending_payment → "bg-yellow-400 text-black"
  paid            → "bg-blue-500 text-white"
  processing      → "bg-indigo-500 text-white"
  shipped         → "bg-cyan-500 text-white"
  completed       → "bg-green-500 text-white"
  cancelled       → "bg-red-500 text-white"
```

### 6.3 Hover Image Swap (CSS Only — Jangan pakai JS)

```blade
{{-- product-card.blade.php --}}
<div class="border-2 border-black group overflow-hidden relative">
    {{-- Gambar utama --}}
    <img src="{{ $product->primaryImage->webp_url }}"
         alt="{{ $product->name }}"
         loading="lazy"
         class="w-full aspect-square object-cover transition-opacity duration-150
                group-hover:opacity-0 absolute inset-0">
    {{-- Gambar hover --}}
    <img src="{{ $product->secondaryImage?->webp_url ?? $product->primaryImage->webp_url }}"
         alt="{{ $product->name }} - tampak belakang"
         loading="lazy"
         class="w-full aspect-square object-cover transition-opacity duration-150
                opacity-0 group-hover:opacity-100">
</div>
```

---

## 7. Panduan Service Classes

### 7.1 RajaOngkirService

```php
// app/Services/RajaOngkirService.php
class RajaOngkirService
{
    public function getCost(int $origin, int $destination, int $weightGrams): array
    {
        $cacheKey = "ongkir:{$origin}:{$destination}:{$weightGrams}";

        return Cache::remember($cacheKey, 2700, function () use ($origin, $destination, $weightGrams) {
            // Hit API RajaOngkir
            // Return array opsi kurir
        });
    }
}
// ⚠️ Inject via constructor injection di Livewire/Controller — jangan new RajaOngkirService()
```

### 7.2 OrderService

```php
// app/Services/OrderService.php
class OrderService
{
    // Buat order dari data checkout — dipanggil dari CheckoutController
    public function createFromCheckout(User $user, array $checkoutData): Order
    {
        return DB::transaction(function () use ($user, $checkoutData) {
            // 1. Validasi ulang harga & stok dari DB (jangan percaya $checkoutData mentah)
            // 2. Buat record Order
            // 3. Buat record OrderItem
            // 4. Kurangi stok (dengan DB lock: select for update)
            // 5. Kosongkan cart user
            // Return Order
        });
    }

    // Dipanggil dari WebhookController setelah payment verified
    public function markAsPaid(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update(['status' => 'paid', 'paid_at' => now()]);
            SendOrderConfirmationEmail::dispatch($order)->onQueue('emails');
        });
    }
}
```

### 7.3 CartService

```php
// app/Services/CartService.php
class CartService
{
    // Merge cart guest ke cart user saat login
    public function mergeGuestCart(User $user, string $sessionId): void
    {
        // Ambil cart_items dengan session_id
        // Untuk setiap item: cek apakah variant sudah ada di cart user
        // Jika ada → tambah qty, jika tidak → update user_id
        // Hapus session_id dari item yang sudah di-merge
    }

    // Validasi stok sebelum checkout
    public function validateStock(Collection $cartItems): array
    {
        // Return array ['valid' => bool, 'errors' => []]
    }
}
```

---

## 8. Panduan Livewire Components

### 8.1 Aturan Livewire

- Jangan panggil API eksternal langsung dari Livewire method — delegasikan ke Service class
- Gunakan `#[Lazy]` untuk component yang loading-nya berat (tabel admin, riwayat transaksi panjang)
- Gunakan `wire:loading` dan skeleton loader untuk UX yang baik
- Validasi di Livewire hanya untuk real-time feedback UI — validasi final tetap di Controller/Service
- Gunakan `$this->dispatch()` untuk komunikasi antar component jika diperlukan

### 8.2 Contoh AddToCart Component

```php
// app/Livewire/Shop/AddToCart.php
class AddToCart extends Component
{
    public Product $product;
    public ?int $selectedVariantId = null;
    public int $qty = 1;
    public string $message = '';

    #[On('variant-selected')]
    public function selectVariant(int $variantId): void
    {
        $this->selectedVariantId = $variantId;
    }

    public function addToCart(CartService $cartService): void
    {
        if (!$this->selectedVariantId) {
            $this->message = 'Pilih ukuran terlebih dahulu.';
            return;
        }

        $variant = ProductVariant::findOrFail($this->selectedVariantId);

        // Validasi stok di server
        if ($variant->stock < $this->qty) {
            $this->message = 'Stok tidak mencukupi.';
            return;
        }

        $cartService->addItem(auth()->user(), $variant, $this->qty);
        $this->dispatch('cart-updated'); // Update badge di header
        $this->message = 'Produk berhasil ditambahkan ke keranjang!';
    }
}
```

---

## 9. Webhook Midtrans — Implementasi Wajib

```php
// app/Http/Controllers/WebhookController.php
class WebhookController extends Controller
{
    public function midtrans(Request $request, OrderService $orderService): Response
    {
        // 1. WAJIB: Verifikasi signature sebelum proses apapun
        $signature = hash(
            'sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            config('midtrans.server_key')
        );

        if ($signature !== $request->signature_key) {
            return response('Unauthorized', 400);
        }

        // 2. Cari order berdasarkan order_number
        $order = Order::where('order_number', $request->order_id)->firstOrFail();

        // 3. Idempotency check — jangan proses ulang jika sudah paid
        if ($order->status === 'paid') {
            return response('OK', 200);
        }

        // 4. Proses berdasarkan transaction_status
        if (in_array($request->transaction_status, ['settlement', 'capture'])) {
            $orderService->markAsPaid($order);
        } elseif (in_array($request->transaction_status, ['cancel', 'expire', 'deny'])) {
            $order->update(['status' => 'cancelled']);
        }

        return response('OK', 200);
    }
}
```

---

## 10. Image Upload — Wajib Konversi WebP

```php
// app/Services/ImageService.php
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    public function storeProductImage(UploadedFile $file, int $productId): string
    {
        $filename = "products/{$productId}/" . uniqid() . '.webp';

        $image = Image::read($file)
            ->scaleDown(width: 1200)   // Resize max 1200px width
            ->toWebp(quality: 80);     // Konversi ke WebP

        Storage::put($filename, $image);

        return $filename;
    }
}
```

Validasi upload di FormRequest:

```php
// app/Http/Requests/Admin/ProductRequest.php
'images.*' => ['required', 'file', 'mimes:jpeg,png,webp', 'max:5120'], // max 5MB
```

---

## 11. Testing & Debugging

### 11.1 Tools Development

```bash
# Install Laravel Debugbar (dev only)
composer require barryvdh/laravel-debugbar --dev

# Pantau N+1 query — aktifkan di .env
DEBUGBAR_ENABLED=true

# Cek query yang dijalankan
\DB::listen(fn($q) => dump($q->sql));
```

### 11.2 Testing Webhook Midtrans di Lokal

```bash
# Install ngrok untuk expose localhost
ngrok http 8000

# Set webhook URL di Midtrans Dashboard Sandbox:
# https://xxxx.ngrok.io/webhooks/midtrans

# Simulasi webhook via Midtrans Sandbox dashboard
# atau gunakan curl:
curl -X POST http://localhost:8000/webhooks/midtrans \
  -H "Content-Type: application/json" \
  -d '{"order_id":"GTT-20240101-00001","transaction_status":"settlement",...}'
```

### 11.3 Testing Queue di Lokal

```bash
# Jalankan queue worker di terminal terpisah
php artisan queue:work --queue=default,emails --tries=3

# Cek email di log (MAIL_MAILER=log)
tail -f storage/logs/laravel.log

# Jika queue stuck, restart
php artisan queue:restart
```

### 11.4 Checklist Sebelum PR / Commit

- [ ] Tidak ada `dd()`, `dump()`, atau `var_dump()` tertinggal di kode
- [ ] Tidak ada hardcoded string yang seharusnya di `.env`
- [ ] Semua route baru sudah punya middleware yang tepat
- [ ] Semua Model baru sudah punya `$fillable` dan relasi
- [ ] Migration baru sudah menambahkan index yang diperlukan
- [ ] Tidak ada N+1 query (cek Debugbar)
- [ ] Image upload sudah dikonversi ke WebP

---

## 12. Urutan Development yang Disarankan

Kerjakan dalam urutan ini untuk menghindari dependency yang belum ada:

```
Phase 1 — Foundation
  [1] Setup Laravel, install semua package
  [2] Buat semua migration sesuai urutan di Section 4
  [3] Buat semua Model dengan $fillable, $casts, dan relasi
  [4] Setup autentikasi (Breeze/Fortify) + tambah kolom whatsapp & role
  [5] Buat middleware EnsureUserIsAdmin & ForceHttps
  [6] Setup routing (web.php) lengkap sesuai Section 5

Phase 2 — Catalog & Cart
  [7] ImageService (upload & konversi WebP)
  [8] Admin: CRUD Produk + Varian + Upload Foto
  [9] Halaman listing produk + filter (Livewire ProductFilter)
  [10] Halaman detail produk + AddToCart Livewire component
  [11] CartService + CartPage Livewire component
  [12] Guest cart + merge saat login (EventServiceProvider: Login event)

Phase 3 — Checkout & Payment
  [13] AddressBook Livewire component (CRUD alamat)
  [14] Proxy endpoint untuk RajaOngkir (dengan caching Redis)
  [15] RajaOngkirService
  [16] CheckoutWizard Livewire (3 step: Alamat → Ongkir → Konfirmasi)
  [17] OrderService::createFromCheckout()
  [18] MidtransPaymentService (Snap token creation)
  [19] WebhookController + OrderService::markAsPaid()
  [20] Job: SendOrderConfirmationEmail

Phase 4 — Dashboard & Admin
  [21] Buyer Dashboard: OrderList Livewire (filter by status)
  [22] Buyer Dashboard: halaman detail pesanan + tracking link
  [23] Admin: Manajemen pesanan + form input resi
  [24] Job: SendShippingNotification
  [25] Admin: Database pelanggan + export CSV
  [26] Admin: Dashboard widget (stats hari ini)

Phase 5 — Optimasi & Finishing
  [27] Database indexing migration
  [28] Semua eager loading (anti N+1)
  [29] WebP lazy loading di semua view
  [30] Vite production build + Tailwind purge
  [31] Artisan cache commands untuk production
  [32] Audit security checklist S-01 s/d S-14 dari PRD
```

---

## 13. Referensi Cepat

### Package yang Digunakan

```bash
# Production
composer require laravel/breeze
composer require livewire/livewire
composer require intervention/image-laravel
composer require midtrans/midtrans-php
composer require guzzlehttp/guzzle          # HTTP client untuk RajaOngkir

# Development
composer require barryvdh/laravel-debugbar --dev
```

### Artisan Commands Berguna

```bash
# Generate Livewire component
php artisan make:livewire Shop/AddToCart

# Generate Job
php artisan make:job SendOrderConfirmationEmail

# Generate FormRequest
php artisan make:request CheckoutRequest

# Generate Service (manual — tidak ada artisan command bawaan)
# Buat file manual di app/Services/

# Clear semua cache
php artisan optimize:clear

# Cache untuk production
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache
```

### Config Penting

```php
// config/hashing.php — Ganti ke Argon2id
'driver' => 'argon2id',

// config/midtrans.php — Buat file ini
return [
    'server_key'     => env('MIDTRANS_SERVER_KEY'),
    'client_key'     => env('MIDTRANS_CLIENT_KEY'),
    'is_production'  => env('MIDTRANS_IS_PRODUCTION', false),
    'snap_url'       => env('MIDTRANS_IS_PRODUCTION')
                            ? 'https://app.midtrans.com/snap/snap.js'
                            : 'https://app.sandbox.midtrans.com/snap/snap.js',
];

// config/rajaongkir.php — Buat file ini
return [
    'api_key'        => env('RAJAONGKIR_API_KEY'),
    'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID'),
    'couriers'       => explode(',', env('RAJAONGKIR_COURIERS', 'jne,jnt,sicepat')),
    'base_url'       => 'https://api.rajaongkir.com/starter',
];
```

---

*AGENTS.md — Gutta E-Commerce v2.0 · Selalu baca file ini di awal setiap sesi kerja.*
