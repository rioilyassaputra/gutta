# Product Requirement Document (PRD) — Gutta E-Commerce Webstore
> **Versi:** 2.0 &nbsp;|&nbsp; **Status:** Ready for Development &nbsp;|&nbsp; **Last Updated:** Juni 2026

---

## 0. Project Overview

| Komponen | Detail |
| :--- | :--- |
| **Nama Produk** | Gutta E-Commerce Webstore |
| **Tech Stack Backend** | Laravel 13, PHP 8.3 |
| **Frontend** | Livewire 3, Alpine.js, Tailwind CSS v3, Vite |
| **Database** | MySQL 8.0 |
| **Cache / Queue** | Redis (ongkir cache, job queue notifikasi) |
| **Storage** | Laravel Storage + Intervention Image (konversi WebP) |
| **Payment Gateway** | Midtrans (Snap) / Xendit — QRIS & Virtual Account |
| **Ongkir API** | RajaOngkir — JNE, J&T, SiCepat, Pos Indonesia |
| **Autentikasi** | Laravel Breeze / Fortify + Laravel Sanctum |
| **Referensi UX/Flow** | alwaysdowhatyoushoulddo.com, seesonss.com |
| **Referensi Visual** | snsbworld.com (Cyber-Streetwear, dark grid, bold borders) |
| **Warna Utama** | `#000000` Hitam Pekat · `#6d28d9` Ungu Gutta · `#ffffff` Putih |
| **Admin Route Prefix** | `/gutta-manage` (bukan `/admin` — security by obscurity) |

---

## 1. Visi Produk & Arahan Desain (UI/UX)

Gutta adalah platform webstore independen untuk produk apparel/streetwear premium. Desain mengadopsi gaya **anti-design** dan **brutalisme modern** yang terorganisir — bukan minimalis generik, tapi *intentional rawness*.

### 1.1 Design System & Visual Language

| Elemen | Spesifikasi |
| :--- | :--- |
| **Grid System** | CSS Grid rigid — `border-2 border-black` atau `border-purple-900` di setiap section dan card |
| **Tipografi** | Inter / Space Grotesk, ultra-bold. Teks minimal, besar, impactful. Tidak ada body text panjang |
| **Warna Base** | Background dominan `#000000`, teks `#ffffff`, aksen tombol `#6d28d9` |
| **Logo Gutta** | Logo resmi Gutta di header, favicon, dan OG image. Warna ungu brand sebagai primary CTA |
| **Product Card** | Foto studio (background netral). Hover → swap ke foto tampak belakang/detail (CSS swap, bukan JS berat) |
| **Tombol CTA** | `rounded-none` (kotak penuh), warna `#6d28d9`, hover → solid `#000000`. Border tebal semua sisi |
| **Animasi** | Hover image swap `150ms ease`. Underline nav on hover. **Tidak ada animasi JS berat** (jaga LCP) |
| **Responsive** | Mobile-first. 1 kolom grid mobile, 2 kolom tablet, 4 kolom desktop |

### 1.2 Referensi Halaman per Tipe

| Halaman | Referensi | Detail Implementasi |
| :--- | :--- | :--- |
| **Homepage / Hero** | snsbworld.com | Full-width hero image/video koleksi terbaru. Tanpa body text panjang. Grid koleksi di bawah fold |
| **Product Listing** | alwaysdowhatyoushoulddo.com | Grid rapat. Filter minimal (kategori, ukuran). Foto mendominasi. Nama produk + harga saja |
| **Product Detail** | seesonss.com | Carousel foto kiri, info kanan. Size chart accordion inline. Tombol Add to Cart sticky di mobile |
| **Checkout** | alwaysdowhatyoushoulddo.com | Flow linear 3 langkah. Progress bar di atas halaman |

---

## 2. Arsitektur Sistem & Database Schema

### 2.1 Arsitektur Aplikasi

```
[ Browser ] ──HTTPS──► [ Nginx ] ──► [ Laravel 11 App (PHP-FPM) ]
                                              │           │           │
                                         [ MySQL ]   [ Redis ]  [ Queue Worker ]
                                                                      │
                                                         [ Mail · Midtrans Webhook ]
```

- Semua request masuk via Nginx, diteruskan ke Laravel via PHP-FPM.
- Livewire component menangani interaktivitas real-time tanpa full page reload.
- Queue worker (Redis) menangani pengiriman email agar tidak memblokir response HTTP.
- Webhook Midtrans/Xendit masuk ke endpoint khusus, terpisah dari routing user.

### 2.2 Database Schema Utama

> Semua tabel menggunakan `deleted_at` (soft delete) dan `created_at` / `updated_at` (timestamps). Foreign key constraint aktif.

#### Tabel: `users`

| Kolom | Tipe | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| `name` | VARCHAR(100) | NOT NULL | Nama lengkap pembeli |
| `email` | VARCHAR(150) | UNIQUE, NOT NULL | Login credential, lowercase |
| `whatsapp` | VARCHAR(20) | NOT NULL | Format: `628xxxxxxxxxx` |
| `password` | VARCHAR(255) | NOT NULL | Argon2id/Bcrypt hash |
| `role` | ENUM | DEFAULT `buyer` | `buyer` \| `admin` |
| `email_verified_at` | TIMESTAMP | NULLABLE | Verifikasi email |
| `remember_token` | VARCHAR(100) | NULLABLE | Session remember |

#### Tabel: `addresses`

| Kolom | Tipe | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, AI | |
| `user_id` | BIGINT UNSIGNED | FK → `users.id`, **INDEX** | |
| `label` | VARCHAR(50) | NOT NULL | Rumah / Kos / Kantor |
| `recipient_name` | VARCHAR(100) | NOT NULL | Nama penerima paket |
| `phone` | VARCHAR(20) | NOT NULL | No. HP penerima |
| `province_id` | INT | NOT NULL | ID Provinsi RajaOngkir |
| `province_name` | VARCHAR(100) | NOT NULL | |
| `city_id` | INT | NOT NULL | ID Kota RajaOngkir |
| `city_name` | VARCHAR(100) | NOT NULL | |
| `district` | VARCHAR(100) | NOT NULL | Kecamatan |
| `postal_code` | VARCHAR(10) | NOT NULL | |
| `full_address` | TEXT | NOT NULL | Jalan, RT/RW, nomor, dll |
| `is_default` | BOOLEAN | DEFAULT `false` | Hanya 1 boleh `true` per user |

#### Tabel: `products`

| Kolom | Tipe | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, AI | |
| `slug` | VARCHAR(200) | UNIQUE, **INDEX** | Auto-generate dari name |
| `name` | VARCHAR(200) | NOT NULL | |
| `description` | TEXT | NULLABLE | |
| `price` | DECIMAL(12,2) | NOT NULL | Harga dasar (belum ongkir) |
| `weight_grams` | INT UNSIGNED | NOT NULL | Berat dalam gram |
| `category_id` | BIGINT UNSIGNED | FK → `categories.id` | |
| `is_active` | BOOLEAN | DEFAULT `true` | Toggle visibilitas |

#### Tabel: `product_variants`

| Kolom | Tipe | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, AI | |
| `product_id` | BIGINT UNSIGNED | FK → `products.id`, **INDEX** | |
| `size` | VARCHAR(10) | NOT NULL | S / M / L / XL / XXL |
| `color` | VARCHAR(50) | NULLABLE | Warna varian jika ada |
| `sku` | VARCHAR(50) | UNIQUE | Stock Keeping Unit |
| `stock` | INT UNSIGNED | NOT NULL, DEFAULT 0 | Stok real-time |
| `additional_price` | DECIMAL(10,2) | DEFAULT 0 | Tambahan harga varian |

#### Tabel: `orders`

| Kolom | Tipe | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, AI | |
| `order_number` | VARCHAR(30) | UNIQUE, **INDEX** | Format: `GTT-YYYYMMDD-XXXXX` |
| `user_id` | BIGINT UNSIGNED | FK → `users.id`, **INDEX** | |
| `address_snapshot` | JSON | NOT NULL | Snapshot alamat saat checkout (immutable) |
| `subtotal` | DECIMAL(12,2) | NOT NULL | Total harga produk sebelum ongkir |
| `shipping_cost` | DECIMAL(10,2) | NOT NULL | Ongkos kirim terpilih |
| `total` | DECIMAL(12,2) | NOT NULL | `subtotal + shipping_cost` |
| `courier` | VARCHAR(50) | NOT NULL | `jne` / `jnt` / `sicepat` |
| `courier_service` | VARCHAR(50) | NOT NULL | `REG` / `YES` / `OKE` dll |
| `status` | ENUM | DEFAULT `pending_payment` | Lihat status flow di bawah |
| `payment_method` | VARCHAR(50) | NULLABLE | `qris` / `bca_va` / `bni_va` dll |
| `payment_token` | VARCHAR(255) | NULLABLE | Token Midtrans/Xendit |
| `paid_at` | TIMESTAMP | NULLABLE | Waktu pembayaran terkonfirmasi |
| `tracking_number` | VARCHAR(50) | NULLABLE, **INDEX** | Nomor resi kurir |
| `shipped_at` | TIMESTAMP | NULLABLE | |
| `completed_at` | TIMESTAMP | NULLABLE | |

#### Order Status Flow

```
pending_payment ──► paid ──► processing ──► shipped ──► completed
        │
        └──► cancelled  (expired 24 jam / user cancel sebelum paid)
```

#### Tabel: `order_items`

| Kolom | Tipe | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, AI | |
| `order_id` | BIGINT UNSIGNED | FK → `orders.id`, INDEX | |
| `product_variant_id` | BIGINT UNSIGNED | FK → `product_variants.id` | |
| `product_name` | VARCHAR(200) | NOT NULL | Snapshot nama produk |
| `variant_detail` | VARCHAR(100) | NOT NULL | Snapshot ukuran/warna |
| `price` | DECIMAL(12,2) | NOT NULL | Snapshot harga saat beli |
| `qty` | INT UNSIGNED | NOT NULL | |
| `subtotal` | DECIMAL(12,2) | NOT NULL | `price * qty` |

#### Tabel: `cart_items`

| Kolom | Tipe | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, AI | |
| `user_id` | BIGINT UNSIGNED | FK → `users.id`, **INDEX** | NULL untuk guest |
| `session_id` | VARCHAR(100) | NULLABLE, INDEX | Untuk guest cart |
| `product_variant_id` | BIGINT UNSIGNED | FK → `product_variants.id` | |
| `qty` | INT UNSIGNED | NOT NULL, DEFAULT 1 | |

---

## 3. Spesifikasi Fitur & Acceptance Criteria

### 3.1 Manajemen Pengguna (User Management)

#### 3.1.1 Registrasi Akun

| Item | Spesifikasi |
| :--- | :--- |
| **Route** | `POST /register` |
| **FormRequest** | `RegisterRequest` — validasi server-side wajib |
| **Field Wajib** | `name` (max:100), `email` (email:rfc,dns, unique:users), `whatsapp` (regex E.164), `password` (min:8, ada angka + simbol, confirmed) |
| **Password Hashing** | `Hash::make()` dengan driver **Argon2id** (set di `config/hashing.php`) |
| **Post-Register** | Auto login → redirect ke `intended()` atau homepage |
| **Email Verifikasi** | Kirim link verifikasi via queue mail. User belum bisa checkout sebelum email terverifikasi |
| **Rate Limit** | `throttle:5,1` (5 request/menit per IP) pada route register |

#### 3.1.2 Login & Session

| Item | Spesifikasi |
| :--- | :--- |
| **Route** | `POST /login` |
| **Credential** | Email + Password |
| **Seamless Redirect** | `intended()` — redirect ke URL terakhir user (produk/cart) setelah login |
| **Remember Me** | Checkbox → session lifetime 30 hari via `remember_token` |
| **Throttle** | `throttle:10,1`. Setelah 5 gagal berturut → lockout 1 menit |
| **Logout** | `POST /logout` → `Auth::logout()` + `Session::invalidate()` + `regenerateToken()` |

#### 3.1.3 Buku Alamat (Address Book)

| Item | Spesifikasi |
| :--- | :--- |
| **Lokasi** | Dashboard Pembeli → tab "Alamat Saya" |
| **Batas Alamat** | Maksimal 5 alamat per user |
| **Pilihan Wilayah** | Dropdown Provinsi → Kota sinkron realtime via AJAX ke endpoint internal Laravel yang mem-proxy RajaOngkir. **API key tidak boleh terekspos ke frontend** |
| **Field** | Label, Nama Penerima, No. HP, Provinsi, Kota, Kecamatan, Kode Pos, Alamat Detail |
| **Alamat Default** | Radio "Jadikan Utama". Hanya 1 default. Auto-terpilih saat checkout |
| **CRUD** | Tambah / Edit / Hapus via Livewire component tanpa page reload |

#### 3.1.4 Dashboard Pembeli & Riwayat Transaksi

| Item | Spesifikasi |
| :--- | :--- |
| **Akses** | `/dashboard` → middleware `auth` + `verified` |
| **Tab Pesanan** | Semua · Menunggu Pembayaran · Diproses · Dikirim · Selesai — filter via Livewire |
| **Card Pesanan** | `order_number`, tanggal, thumbnail produk (maks 3), total, status badge berwarna |
| **Pelacakan Resi** | Status `shipped` → tombol "Lacak Paket" → deeplink URL tracking kurir (JNE/J&T/SiCepat) |
| **Timer Pembayaran** | Status `pending_payment` → countdown timer 24 jam + tombol "Bayar Sekarang" (buka kembali Snap Midtrans) |
| **Detail Pesanan** | Klik card → halaman detail: list item, alamat, metode bayar, resi, timeline status |

---

### 3.2 Katalog & Halaman Produk

#### 3.2.1 Halaman Listing Produk

| Item | Spesifikasi |
| :--- | :--- |
| **Route** | `GET /products` atau `/collections/{category:slug}` |
| **Grid** | 4 kolom desktop · 2 kolom tablet · 1 kolom mobile |
| **Filter** | Kategori (tabs), Ukuran (checkbox), Harga (range slider) — via Livewire, tanpa reload |
| **Sorting** | Terbaru · Harga Terendah · Harga Tertinggi |
| **Pagination** | Livewire pagination — load more atau infinite scroll |
| **Product Card** | Foto utama + hover swap ke foto ke-2. Badge `SOLD OUT` jika semua varian stok = 0 |
| **SEO** | Meta title & description dinamis per kategori. Canonical URL. Sitemap otomatis |

#### 3.2.2 Halaman Detail Produk

| Item | Spesifikasi |
| :--- | :--- |
| **Route** | `GET /products/{product:slug}` |
| **Layout** | Carousel foto kiri (swipe mobile), info kanan: nama, harga, deskripsi, pilih ukuran |
| **Pilih Ukuran** | Button grid. Stok 0 → disabled + strikethrough. Wajib pilih ukuran sebelum Add to Cart |
| **Size Chart** | Toggle accordion inline — tidak redirect ke halaman lain |
| **Stok Indicator** | Tampilkan "Sisa X item" jika stok ≤ 5, sembunyikan jika > 5 |
| **Add to Cart** | Livewire action: cek stok real-time di server → tambah ke cart → update badge header tanpa reload |
| **Foto WebP** | Semua foto dikonversi ke `.webp` saat upload. `loading="lazy"` untuk foto di bawah fold |

---

### 3.3 Modul Cart & Checkout

#### 3.3.1 Keranjang Belanja

| Item | Spesifikasi |
| :--- | :--- |
| **Storage** | DB tabel `cart_items` (user login) · Session (guest — merge saat login) |
| **Add to Cart** | Cek stok variant real-time di server. Return Livewire error jika stok tidak cukup |
| **Race Condition** | Stok tinggal 1 → tombol `+` di-lock otomatis. Validasi final di server saat checkout |
| **Update Qty** | Tombol `+/-` → Livewire update → subtotal & grand total diperbarui instan di client, divalidasi ulang di server |
| **Hapus Item** | Tombol hapus per item. Opsi "Kosongkan Keranjang" |
| **Subtotal** | `SUM(harga_varian * qty)`. Belum termasuk ongkir. Sidebar sticky |
| **Guest Cart** | Simpan di session. Saat login, cart guest di-merge ke cart user yang sudah ada (tidak hilang) |

#### 3.3.2 Flow Checkout (3 Step Linear)

```
STEP 1: Konfirmasi Produk & Pilih Alamat
    ↓
STEP 2: Pilih Kurir & Layanan Pengiriman
    ↓
STEP 3: Konfirmasi Order & Pembayaran
```

| Step | Aksi User | Detail Implementasi |
| :--- | :--- | :--- |
| **1 — Alamat** | Pilih dari Buku Alamat atau input alamat baru | Tampilkan list alamat tersimpan. "Tambah Alamat Baru" → modal Livewire. Progress bar aktif di step 1 |
| **2 — Ongkir** | Pilih kurir & layanan | Otomatis trigger kalkulasi ongkir RajaOngkir. Tampilkan: nama kurir, layanan, estimasi hari, harga. User pilih satu → update total |
| **3 — Konfirmasi** | Review & bayar | Ringkasan: produk, alamat, kurir, subtotal, ongkir, **TOTAL**. Tombol "Bayar Sekarang" → server buat Order di DB → redirect ke Snap Midtrans |

---

### 3.4 Integrasi API Ongkos Kirim (RajaOngkir)

| Item | Spesifikasi |
| :--- | :--- |
| **Provider** | RajaOngkir (starter/pro plan) |
| **Trigger** | User pilih alamat di step 2 → Livewire dispatch event → hit endpoint internal Laravel |
| **Parameter** | `origin`: ID Kota Gudang Gutta (hardcoded `config/rajaongkir.php`), `destination`: `city_id` alamat terpilih, `weight`: total berat semua item dalam gram (`SUM(weight_grams * qty)`) |
| **Kurir** | JNE, J&T, SiCepat, Pos Indonesia — konfigurasi di `.env` `RAJAONGKIR_COURIERS` |
| **Output** | List opsi: nama kurir, kode layanan (REG/YES/OKE), estimasi hari, tarif (Rupiah) |
| **Caching** | Redis key: `ongkir:{origin}:{destination}:{weight}` TTL 45 menit (`Cache::remember()`) |
| **Error Handling** | API timeout/gagal → tampilkan pesan retry, **jangan blokir checkout** |
| **Security** | API key hanya di `.env`, tidak pernah terekspos ke JavaScript/frontend |

---

### 3.5 Payment Gateway (Midtrans / Xendit)

| Item | Spesifikasi |
| :--- | :--- |
| **Provider** | Midtrans (Snap) — dapat diganti Xendit via interface abstraksi `PaymentService` |
| **Metode** | QRIS (QR di layar), Virtual Account: BCA, Mandiri, BNI, BRI, Permata |
| **Alur Pembayaran** | 1) Order dibuat di DB (`pending_payment`) → 2) Server minta Snap Token → 3) Redirect ke Snap popup → 4) User bayar → 5) Midtrans kirim webhook |
| **Order Expiry** | Set expiry 24 jam di Midtrans payload. Setelah expire → status `cancelled` via webhook atau cron job |
| **Webhook Endpoint** | `POST /webhooks/midtrans` — tidak perlu auth session, diverifikasi via **HMAC-SHA512** signature key Midtrans |
| **Webhook Action** | `transaction_status = settlement/capture` → update status → `paid`, kurangi stok, dispatch `SendOrderConfirmationEmail` ke queue |
| **Idempotency** | Cek `order_number` sebelum proses webhook. Jika sudah `paid`, skip (prevent double-process dari retry webhook) |
| **Sandbox** | Gunakan Midtrans Sandbox di dev/staging. Toggle via `MIDTRANS_IS_PRODUCTION` di `.env` |

---

### 3.6 Dashboard Admin (Panel Pengelola)

| Item | Spesifikasi |
| :--- | :--- |
| **Akses** | `/gutta-manage` — middleware `auth` + `EnsureUserIsAdmin` (cek `role = admin`) |
| **Dashboard Home** | Widget: Total Pesanan Hari Ini · Revenue Hari Ini · Pesanan Perlu Dikemas · Stok Hampir Habis (< 5 unit) |
| **Manajemen Produk** | CRUD produk + varian + upload multiple foto (konversi otomatis ke WebP). Drag-and-drop urutan foto. Toggle aktif/nonaktif |
| **Manajemen Pesanan** | Filter: status, tanggal, kurir. Search: `order_number`, nama, email. Klik → detail + form input nomor resi |
| **Input Resi** | Submit resi → update `tracking_number` + `shipped_at`, dispatch `SendShippingNotification` ke queue, update status dashboard user |
| **Database Pelanggan** | Tabel: nama, email, WhatsApp, total pesanan, total spend. Export CSV. Filter berdasarkan tanggal daftar |
| **Manajemen Stok** | Per varian: input stok, lihat history perubahan stok (audit log sederhana) |

---

## 4. Spesifikasi Keamanan (Security Requirements)

> ⚠️ Seluruh item di bawah adalah **NON-NEGOTIABLE** dan wajib diimplementasikan sebelum launch ke production.

| Kode | Requirement | Implementasi Laravel | Prioritas |
| :--- | :--- | :--- | :---: |
| **S-01** | CSRF Protection | Semua form `POST/PUT/DELETE` wajib `@csrf`. Livewire handles CSRF otomatis via `X-CSRF-TOKEN` | 🔴 CRITICAL |
| **S-02** | SQL Injection Prevention | Wajib Eloquent ORM / Query Builder dengan parameter binding. **Dilarang** `DB::statement()` dengan string interpolasi | 🔴 CRITICAL |
| **S-03** | Password Hashing | `Hash::make()` dengan driver **Argon2id** (set di `config/hashing.php`). Bcrypt minimum jika hosting tidak support Argon2 | 🔴 CRITICAL |
| **S-04** | Webhook Verification | Verifikasi HMAC-SHA512 signature dari header Midtrans sebelum proses payload. Tolak jika tidak cocok → HTTP 400 | 🔴 CRITICAL |
| **S-05** | Server-Side Validation | Semua data checkout (harga, berat, `product_id`, `variant_id`) divalidasi ulang di Controller/Service. **Jangan percaya data dari client** | 🔴 CRITICAL |
| **S-06** | Authentication Guard | Route `/dashboard/*` → middleware `auth`. Route `/gutta-manage/*` → tambah middleware `role:admin` | 🟠 HIGH |
| **S-07** | Rate Limiting | `throttle:5,1` untuk `/register` · `throttle:10,1` untuk `/login` · `throttle:30,1` untuk `/webhooks/*` | 🟠 HIGH |
| **S-08** | XSS Prevention | Gunakan `{{ $var }}` (bukan `{!! !!}`) untuk semua output user-generated content di Blade | 🟠 HIGH |
| **S-09** | File Upload Security | Validasi MIME type (`image/jpeg`, `image/png`, `image/webp`), max 5MB, simpan di `storage/app/private`, serve via controller route | 🟠 HIGH |
| **S-10** | Secure `.env` | API key RajaOngkir, Midtrans, SMTP hanya di `.env`. Tidak hardcode di kode. `.env` di `.gitignore` | 🟠 HIGH |
| **S-11** | HTTPS Only | `APP_URL` dengan `https://`. Middleware `ForceHttps` di production. HSTS header aktif | 🟠 HIGH |
| **S-12** | Mass Assignment Protection | Definisikan `$fillable` di setiap Model. Hindari `Model::create($request->all())` | 🟡 MEDIUM |
| **S-13** | Session Security | `SESSION_DRIVER=redis` di production. `SESSION_SECURE_COOKIE=true`. `SESSION_SAME_SITE=strict` | 🟡 MEDIUM |
| **S-14** | Admin Route Obfuscation | Prefix admin route `/gutta-manage` bukan `/admin` — security by obscurity | 🟢 LOW |

---

## 5. Spesifikasi Kecepatan & Performa

> **Target Core Web Vitals (production):** LCP < 2.5s · INP < 100ms · CLS < 0.1
> **Target Lighthouse Score:** ≥ 80 mobile

| Kode | Optimasi | Implementasi | Impact |
| :--- | :--- | :--- | :---: |
| **P-01** | Caching Ongkir | `Cache::remember("ongkir:{origin}:{dest}:{weight}", 2700, fn() => ...)` — TTL 45 menit di Redis | 🔴 HIGH |
| **P-02** | Eager Loading (Anti N+1) | Catalog: `Product::with(['variants', 'images', 'category'])->paginate()`. Orders: `Order::with(['items.variant.product'])`. Deteksi dengan Laravel Debugbar di dev | 🔴 HIGH |
| **P-03** | Database Indexing | Index wajib: `users.email`, `orders.user_id`, `orders.order_number`, `orders.status`, `products.slug`, `cart_items.user_id`, `product_variants.product_id` | 🔴 HIGH |
| **P-04** | Gambar WebP & Lazy Load | Intervention Image: konversi ke WebP, resize max 1200px, compress quality 80 saat upload. `<img loading="lazy">` untuk gambar di bawah fold | 🔴 HIGH |
| **P-05** | Vite Asset Bundling | `npm run build`: Vite minify JS+CSS, Tailwind purge unused classes. Output fingerprinted. Asset via versioned URL | 🟠 MEDIUM |
| **P-06** | Route & Config Cache | Deployment script: `php artisan config:cache && route:cache && view:cache && event:cache` | 🟠 MEDIUM |
| **P-07** | Queue untuk Email | Semua email (konfirmasi order, notifikasi resi, verifikasi) via Laravel Queue → Redis driver. Tidak memblokir HTTP response | 🟠 MEDIUM |
| **P-08** | Livewire Lazy Loading | Component berat (tabel admin, riwayat transaksi) gunakan `#[Lazy]` attribute Livewire 3. Tampilkan skeleton loader saat loading | 🟠 MEDIUM |
| **P-09** | DB Connection Pool | Pastikan persistent connection aktif. Pertimbangkan ProxySQL/PgBouncer jika traffic tinggi | 🟢 LOW |
| **P-10** | PHP OPcache | Aktifkan di production: `opcache.enable=1`, `opcache.memory_consumption=256`. Revalidate tiap 60 detik | 🟢 LOW |

---

## 6. Deployment & Environment

### 6.1 Environment per Stage

| Item | Development | Staging | Production |
| :--- | :---: | :---: | :---: |
| `APP_DEBUG` | `true` | `false` | `false` |
| `APP_ENV` | `local` | `staging` | `production` |
| Cache Driver | `file` / `array` | `redis` | `redis` |
| Queue Driver | `sync` | `redis` | `redis` (Supervisor multi-process) |
| Mail Driver | `log` / Mailtrap | Mailtrap / SMTP | SMTP (SES / Mailgun) |
| Midtrans Mode | Sandbox | Sandbox | Production |
| HTTPS | Opsional | Wajib | Wajib + HSTS |
| Artisan Cache | — | `config:cache` | `config + route + view + event` cache |

### 6.2 Environment Variables Wajib (`.env`)

```env
# App
APP_KEY=
APP_URL=https://gutta.id
APP_ENV=production
APP_DEBUG=false

# Database
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Redis
REDIS_HOST=
REDIS_PASSWORD=
REDIS_PORT=6379

# Mail
MAIL_HOST=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@gutta.id
MAIL_FROM_NAME="Gutta Store"

# Midtrans
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=true

# RajaOngkir
RAJAONGKIR_API_KEY=
RAJAONGKIR_ORIGIN_CITY_ID=
RAJAONGKIR_COURIERS=jne,jnt,sicepat,pos

# Session & Security
SESSION_DRIVER=redis
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
FILESYSTEM_DISK=local
```

---

## 7. Panduan Struktur Kode Laravel

### 7.1 Konvensi Penamaan & Lokasi File

| Komponen | Lokasi | Contoh |
| :--- | :--- | :--- |
| **Livewire Components** | `app/Livewire/` | `AddToCart.php`, `CartPage.php`, `CheckoutWizard.php` |
| **Services** | `app/Services/` | `RajaOngkirService.php`, `PaymentService.php`, `OrderService.php` |
| **Form Requests** | `app/Http/Requests/` | `RegisterRequest.php`, `CheckoutRequest.php` |
| **Models** | `app/Models/` | `User`, `Product`, `ProductVariant`, `Order`, `OrderItem`, `Address`, `CartItem` |
| **Controllers** | `app/Http/Controllers/` | `ProductController`, `OrderController`, `WebhookController`, `Admin/OrderController` |
| **Jobs** | `app/Jobs/` | `SendOrderConfirmationEmail.php`, `SendShippingNotification.php` |
| **Middleware** | `app/Http/Middleware/` | `EnsureUserIsAdmin.php`, `ForceHttps.php` |
| **Blade Views** | `resources/views/` | `layouts/`, `components/`, `livewire/`, `pages/shop/`, `pages/checkout/`, `pages/dashboard/`, `pages/admin/` |

### 7.2 Service Pattern (Abstraksi Payment)

```php
// app/Services/PaymentService.php
interface PaymentServiceInterface {
    public function createTransaction(Order $order): array;
    public function verifyWebhook(array $payload, string $signature): bool;
}

// Implementasi bisa ganti antara Midtrans / Xendit
// tanpa ubah logika di Controller
class MidtransPaymentService implements PaymentServiceInterface { ... }
class XenditPaymentService implements PaymentServiceInterface { ... }
```

---

## 8. Changelog

| Versi | Tanggal | Perubahan |
| :--- | :--- | :--- |
| **v1.0** | — | Draft awal: fitur dasar User, Cart, Ongkir, Payment, Admin |
| **v2.0** | Juni 2026 | Penambahan: database schema lengkap, security matrix S-01–S-14, performance checklist P-01–P-10, deployment environment table, struktur kode & service pattern, order status flow diagram, guest cart, idempotency webhook, admin dashboard widget, tabel `order_items` & `cart_items` |

---

*— End of Document — Gutta E-Commerce PRD v2.0*
