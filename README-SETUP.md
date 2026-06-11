# Panduan Setup Proyek BryanRO — Laravel

**Sistem Informasi Berbasis WhatsApp Gateway untuk Depot Air Galon BryanRO**
Fauzan Respati Indira — NIM 201011400445 — Universitas Pamulang — 2026

---

## Prasyarat

Pastikan sudah terinstall di komputer:
- **MAMP** (sudah punya) — untuk Apache + MySQL + PHP
- **Composer** — package manager PHP → unduh di https://getcomposer.org/
- **PHP 8.2** (sudah ada di MAMP)

---

## Langkah 1: Buat Project Laravel

Buka Terminal (Mac) atau Command Prompt (Windows), jalankan:

```bash
composer create-project laravel/laravel bryanro
```

Ini akan membuat folder `bryanro/` berisi project Laravel baru.

---

## Langkah 2: Salin File dari Folder Ini

Salin semua file dari folder `bryanro-laravel/` (hasil dari Replit) ke dalam folder `bryanro/` yang baru dibuat.

**File/folder yang perlu disalin (timpa yang sudah ada):**

```
bryanro-laravel/
├── routes/web.php                          → bryanro/routes/web.php
├── app/Http/Controllers/                   → bryanro/app/Http/Controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── PelangganController.php
│   ├── ProdukController.php
│   ├── PesananController.php
│   └── LaporanController.php
├── app/Http/Middleware/CekLogin.php        → bryanro/app/Http/Middleware/
├── app/Models/                             → bryanro/app/Models/
│   ├── Admin.php
│   ├── Pelanggan.php
│   ├── Produk.php
│   ├── Pesanan.php
│   ├── DetailPesanan.php
│   └── NotifikasiWa.php
├── app/Services/WhatsAppService.php       → bryanro/app/Services/
├── resources/views/                        → bryanro/resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/login.blade.php
│   ├── dashboard/index.blade.php
│   ├── pelanggan/index.blade.php
│   ├── produk/index.blade.php
│   ├── pesanan/index.blade.php
│   └── laporan/index.blade.php
├── database/seeders/AdminSeeder.php       → bryanro/database/seeders/
└── config/services.php                    → bryanro/config/services.php
```

---

## Langkah 3: Daftarkan Middleware di Kernel

Buka file `bryanro/app/Http/Kernel.php`, cari bagian `$routeMiddleware`, lalu **tambahkan** baris berikut:

```php
protected $routeMiddleware = [
    // ... (yang sudah ada, jangan dihapus)
    'auth'       => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    // ... dst

    // TAMBAHKAN BARIS INI:
    'cek.login'  => \App\Http\Middleware\CekLogin::class,
];
```

> **Catatan untuk Laravel 11:** Jika tidak ada file `Kernel.php`, buka `bootstrap/app.php` dan tambahkan:
> ```php
> ->withMiddleware(function (Middleware $middleware) {
>     $middleware->alias([
>         'cek.login' => \App\Http\Middleware\CekLogin::class,
>     ]);
> })
> ```

---

## Langkah 4: Konfigurasi File .env

Di folder `bryanro/`, salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Lalu edit file `.env` dengan teks editor dan sesuaikan:

```env
APP_NAME="BryanRO"
APP_URL=http://localhost:8888/bryanro/public

# Database — sesuaikan dengan setting MAMP kamu
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889        # Port MySQL MAMP (cek di MAMP > Preferences > Ports)
DB_DATABASE=bryanro
DB_USERNAME=root
DB_PASSWORD=root    # Password default MAMP

# WhatsApp Gateway Fonnte (isi nanti setelah daftar di fonnte.com)
FONNTE_TOKEN=ISI_TOKEN_FONNTE_DISINI
```

> **Cek port MAMP:** Buka MAMP → Preferences → Ports → lihat "MySQL Port" (biasanya 8889)

---

## Langkah 5: Generate App Key

Jalankan di terminal (di dalam folder `bryanro/`):

```bash
php artisan key:generate
```

Ini akan mengisi `APP_KEY` di file `.env` secara otomatis.

---

## Langkah 6: Import Database

1. Pastikan MAMP sudah berjalan (Apache + MySQL hijau)
2. Buka browser → http://localhost:8888/phpMyAdmin (sesuaikan port MAMP kamu)
3. Buat database baru bernama `bryanro` (jika belum ada)
4. Pilih database `bryanro` → klik tab **Import**
5. Pilih file `bryanro_1778522487598.sql` → klik **Go**

---

## Langkah 7: Buat Akun Admin

Jalankan seeder untuk membuat akun admin pertama:

```bash
php artisan db:seed --class=AdminSeeder
```

Ini akan membuat akun:
- **Username:** `admin`
- **Password:** `admin123`

> Atau, masukkan manual via phpMyAdmin:
> 1. Buka tabel `admin`
> 2. Klik "Insert"
> 3. Isi username: `admin`
> 4. Untuk password, jalankan dulu: `php artisan tinker` → `echo Hash::make('admin123');`
> 5. Salin hasilnya ke kolom password

---

## Langkah 8: Jalankan Aplikasi

**Cara 1: Via php artisan serve** (lebih mudah)
```bash
cd bryanro
php artisan serve
```
Buka browser: http://127.0.0.1:8000

**Cara 2: Via MAMP htdocs** (seperti project PHP biasa)
1. Salin seluruh folder `bryanro/` ke `MAMP/htdocs/`
2. Buka browser: http://localhost:8888/bryanro/public

---

## Langkah 9: Setup WhatsApp Gateway (Fonnte)

1. Buka https://fonnte.com/ dan daftar akun
2. Di dashboard Fonnte, klik **"Connect Device"**
3. Scan QR code menggunakan WhatsApp di HP yang akan dipakai sebagai pengirim notifikasi
4. Setelah terhubung, salin **Token** dari dashboard
5. Buka file `.env`, ubah:
   ```
   FONNTE_TOKEN=token_yang_kamu_salin_dari_fonnte
   ```
6. Restart server: `php artisan serve`

Setelah ini, setiap kali admin tambah pesanan atau update status → WA otomatis terkirim ke pelanggan!

---

## Struktur File Penting

```
bryanro/
├── routes/web.php                  ← Semua URL / route aplikasi
├── app/
│   ├── Http/
│   │   ├── Controllers/            ← Logika bisnis setiap halaman
│   │   └── Middleware/
│   │       └── CekLogin.php        ← Cek apakah admin sudah login
│   ├── Models/                     ← Representasi tabel database
│   └── Services/
│       └── WhatsAppService.php     ← Kirim WA via Fonnte API
├── resources/views/                ← Tampilan HTML (Blade template)
│   ├── layouts/app.blade.php       ← Layout utama (sidebar)
│   ├── auth/login.blade.php        ← Halaman login
│   ├── dashboard/index.blade.php   ← Dashboard admin
│   ├── pelanggan/index.blade.php   ← Daftar pelanggan
│   ├── produk/index.blade.php      ← Data produk
│   ├── pesanan/index.blade.php     ← Daftar pesanan (+ WA gateway)
│   └── laporan/index.blade.php     ← Laporan penjualan
├── config/services.php             ← Konfigurasi Fonnte API
└── .env                            ← Konfigurasi database & token WA
```

---

## Akun Default

| Username | Password  | Keterangan              |
|----------|-----------|-------------------------|
| admin    | admin123  | Dibuat oleh AdminSeeder |

> ⚠️ Ganti password setelah pertama kali login!

---

## Troubleshooting

**Error: "No application encryption key has been specified"**
→ Jalankan: `php artisan key:generate`

**Error: "Connection refused" saat konek database**
→ Cek port MySQL di MAMP Preferences. Ganti `DB_PORT` di `.env` sesuai port yang terlihat di MAMP.

**Error: "Route [login] not defined"**
→ Pastikan file `routes/web.php` sudah disalin dengan benar.

**Error: "Class 'App\Http\Middleware\CekLogin' not found"**
→ Pastikan middleware sudah didaftarkan di `app/Http/Kernel.php` (lihat Langkah 3).

**Pesan WA tidak terkirim**
→ Cek FONNTE_TOKEN di `.env`. Lihat log error di `storage/logs/laravel.log`.

**Halaman putih / 500 Error**
→ Aktifkan debug: `APP_DEBUG=true` di `.env`, lalu refresh halaman untuk melihat pesan error.
