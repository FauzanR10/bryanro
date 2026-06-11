<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- @yield('title') diisi oleh masing-masing halaman yang extend layout ini --}}
    <title>@yield('title', 'BryanRO') — Sistem Informasi Depot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--
        Tailwind CSS via CDN Play
        Tidak perlu npm install / build — langsung bisa dipakai.
        Untuk produksi, disarankan install Tailwind CSS via npm untuk performa lebih baik.
    --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Konfigurasi warna kustom Tailwind --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Warna utama BryanRO (biru)
                        brand: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Tambahan CSS kustom per halaman --}}
    @stack('styles')
</head>

<body class="bg-gray-100 font-sans text-sm antialiased">

    {{--
        ═══════════════════════════════════════════
        LAYOUT UTAMA: SIDEBAR + KONTEN
        ═══════════════════════════════════════════
        Menggunakan flexbox:
        - Sidebar  : lebar tetap 224px (w-56), tinggi full screen
        - Konten   : sisa lebar layar, scrollable
    --}}
    <div class="flex h-screen overflow-hidden">

        {{-- ══════════════════════════════════════
             SIDEBAR NAVIGASI (kiri)
             Desain: latar biru tua, teks putih
             ══════════════════════════════════════ --}}
        <aside class="w-56 bg-blue-700 flex flex-col flex-shrink-0 overflow-y-auto">

            {{-- Logo & nama aplikasi --}}
            <div class="px-5 py-5 border-b border-blue-600">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-700 font-bold text-sm">BR</span>
                    </div>
                    <div>
                        <p class="text-white font-bold text-base leading-tight">BryanRO</p>
                        <p class="text-blue-200 text-xs">Sistem Informasi Depot</p>
                    </div>
                </div>
            </div>

            {{-- Menu navigasi --}}
            <nav class="flex-1 px-3 py-4 space-y-1">

                {{--
                    Setiap item menu dicek apakah URL saat ini cocok dengan route-nya.
                    Jika cocok → background putih (aktif), jika tidak → transparan (tidak aktif).
                    request()->routeIs('nama.route') → true jika URL saat ini = route tersebut
                --}}

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('dashboard') ? 'bg-white text-blue-700 font-semibold' : 'text-blue-100 hover:bg-blue-600' }}">
                    {{-- Ikon rumah (SVG inline) --}}
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                {{-- Daftar Pesanan --}}
                <a href="{{ route('pesanan.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('pesanan.*') ? 'bg-white text-blue-700 font-semibold' : 'text-blue-100 hover:bg-blue-600' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Daftar Pesanan
                </a>

                {{-- Data Pelanggan --}}
                <a href="{{ route('pelanggan.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('pelanggan.*') ? 'bg-white text-blue-700 font-semibold' : 'text-blue-100 hover:bg-blue-600' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Data Pelanggan
                </a>

                {{-- Data Produk --}}
                <a href="{{ route('produk.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('produk.*') ? 'bg-white text-blue-700 font-semibold' : 'text-blue-100 hover:bg-blue-600' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Data Produk
                </a>

                {{-- Laporan Penjualan --}}
                <a href="{{ route('laporan.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('laporan.*') ? 'bg-white text-blue-700 font-semibold' : 'text-blue-100 hover:bg-blue-600' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Laporan Penjualan
                </a>

            </nav>

            {{-- Info admin yang sedang login + tombol logout --}}
            <div class="px-3 py-4 border-t border-blue-600">
                {{-- Nama admin dari session --}}
                <div class="flex items-center gap-2 px-3 py-2 mb-2">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold uppercase">
                            {{ substr(session('admin_username', 'A'), 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-white text-xs font-medium">{{ session('admin_username', 'Admin') }}</p>
                        <p class="text-blue-300 text-xs">BryanRO</p>
                    </div>
                </div>

                {{-- Form logout — menggunakan POST sesuai route yang sudah dibuat --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf {{-- Token CSRF wajib untuk form POST di Laravel --}}
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 text-blue-200 hover:bg-blue-600 rounded-lg text-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>

        </aside>
        {{-- /sidebar --}}

        {{-- ══════════════════════════════════════
             AREA KONTEN UTAMA (kanan)
             ══════════════════════════════════════ --}}
        <main class="flex-1 flex flex-col overflow-hidden">

            {{-- ── Notifikasi Flash Message ─────────────────────
                 Muncul di atas konten setelah redirect dengan ->with('success'/'error')
            --}}
            @if(session('success'))
                <div class="mx-6 mt-4 px-4 py-3 bg-green-50 border border-green-300 rounded-lg flex items-start gap-2 flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700 text-xs">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-6 mt-4 px-4 py-3 bg-red-50 border border-red-300 rounded-lg flex items-start gap-2 flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-red-700 text-xs">{{ session('error') }}</p>
                </div>
            @endif

            {{-- ── Konten halaman (diisi oleh masing-masing view) ── --}}
            <div class="flex-1 overflow-y-auto">
                @yield('konten')
            </div>

        </main>
        {{-- /main --}}

    </div>
    {{-- /flex layout --}}

    {{-- Script tambahan per halaman --}}
    @stack('scripts')

</body>
</html>
