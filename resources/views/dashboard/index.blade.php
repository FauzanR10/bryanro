{{--
    HALAMAN DASHBOARD ADMIN
    =======================
    Halaman pertama yang dilihat admin setelah login.
    Menampilkan ringkasan data dan 5 pesanan terbaru.

    Extends: layouts/app.blade.php (sidebar + header utama)
--}}
@extends('layouts.app')
@section('title', 'Dashboard')

@section('konten')
<div class="px-6 py-5 space-y-5">

    {{-- ── Header Halaman ────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Dashboard</h1>
            <p class="text-xs text-gray-500">Selamat datang, <strong>{{ session('admin_username') }}</strong>! Berikut ringkasan data BryanRO hari ini.</p>
        </div>
        {{-- Tanggal hari ini --}}
        <div class="text-right">
            <p class="text-xs text-gray-400">{{ now()->translatedFormat('l, d F Y') }}</p>
            <p class="text-xs text-gray-400">{{ now()->format('H:i') }} WIB</p>
        </div>
    </div>

    {{-- ── Kartu Statistik (4 kartu) ─────────────────── --}}
    <div class="grid grid-cols-4 gap-4">

        {{-- Kartu 1: Total Pelanggan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Pelanggan</p>
                <p class="text-2xl font-bold text-blue-600">{{ $totalPelanggan }}</p>
                <p class="text-xs text-gray-400">pelanggan terdaftar</p>
            </div>
        </div>

        {{-- Kartu 2: Total Pesanan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Pesanan</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $totalPesanan }}</p>
                <p class="text-xs text-gray-400">semua waktu</p>
            </div>
        </div>

        {{-- Kartu 3: Pendapatan Bulan Ini --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pendapatan Bulan Ini</p>
                <p class="text-xl font-bold text-green-600">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">dari pesanan selesai</p>
            </div>
        </div>

        {{-- Kartu 4: WA Terkirim --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">WA Terkirim</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $totalWaTerkirim }}</p>
                <p class="text-xs text-gray-400">notifikasi berhasil</p>
            </div>
        </div>

    </div>

    {{-- ── Status Pesanan & Aksi Cepat ────────────────── --}}
    <div class="grid grid-cols-3 gap-4">

        {{-- Status pesanan ringkasan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 col-span-1">
            <h3 class="font-bold text-gray-700 text-sm mb-3">Status Pesanan</h3>
            <div class="space-y-2">
                <div class="flex items-center justify-between py-2 px-3 bg-yellow-50 rounded-lg">
                    <span class="text-xs text-yellow-700 font-medium">⏳ Pending</span>
                    <span class="text-sm font-bold text-yellow-700">{{ $pesananPending }}</span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 bg-orange-50 rounded-lg">
                    <span class="text-xs text-orange-700 font-medium">🚚 Proses</span>
                    <span class="text-sm font-bold text-orange-700">{{ $pesananProses }}</span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 bg-green-50 rounded-lg">
                    <span class="text-xs text-green-700 font-medium">✅ Selesai</span>
                    <span class="text-sm font-bold text-green-700">{{ $pesananSelesai }}</span>
                </div>
            </div>
            <a href="{{ route('pesanan.index') }}"
               class="mt-3 block w-full text-center py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                Lihat Semua Pesanan →
            </a>
        </div>

        {{-- Aksi cepat --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 col-span-2">
            <h3 class="font-bold text-gray-700 text-sm mb-3">Aksi Cepat</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('pesanan.index') }}"
                   class="flex items-center gap-3 p-3 border border-blue-200 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                    <span class="text-xl">📋</span>
                    <div>
                        <p class="text-xs font-bold text-blue-800">Tambah Pesanan</p>
                        <p class="text-xs text-blue-500">Catat pesanan baru</p>
                    </div>
                </a>
                <a href="{{ route('pelanggan.index') }}"
                   class="flex items-center gap-3 p-3 border border-teal-200 bg-teal-50 hover:bg-teal-100 rounded-xl transition-colors">
                    <span class="text-xl">👤</span>
                    <div>
                        <p class="text-xs font-bold text-teal-800">Data Pelanggan</p>
                        <p class="text-xs text-teal-500">Kelola pelanggan</p>
                    </div>
                </a>
                <a href="{{ route('laporan.index') }}"
                   class="flex items-center gap-3 p-3 border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors">
                    <span class="text-xl">📊</span>
                    <div>
                        <p class="text-xs font-bold text-indigo-800">Laporan Penjualan</p>
                        <p class="text-xs text-indigo-500">Lihat statistik</p>
                    </div>
                </a>
                <a href="{{ route('produk.index') }}"
                   class="flex items-center gap-3 p-3 border border-cyan-200 bg-cyan-50 hover:bg-cyan-100 rounded-xl transition-colors">
                    <span class="text-xl">📦</span>
                    <div>
                        <p class="text-xs font-bold text-cyan-800">Data Produk</p>
                        <p class="text-xs text-cyan-500">Kelola produk & harga</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- ── Tabel 5 Pesanan Terbaru ─────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 text-sm">Pesanan Terbaru</h2>
            <a href="{{ route('pesanan.index') }}" class="text-xs text-blue-600 hover:underline">Lihat semua →</a>
        </div>

        @if($pesananTerbaru->isEmpty())
            <div class="px-5 py-10 text-center text-gray-400 text-xs">
                Belum ada pesanan. Mulai tambahkan pesanan pertama!
            </div>
        @else
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">No. Pesanan</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Pelanggan</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Produk</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Total</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pesananTerbaru as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-mono text-blue-600 font-semibold">
                                #{{ str_pad($p->id_pesanan, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $p->pelanggan->nama ?? '-' }}
                                <p class="text-gray-400 font-normal">{{ $p->pelanggan->no_wa ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @foreach($p->detail as $d)
                                    {{ $d->produk->nama_produk ?? '-' }} ({{ $d->jumlah }}x)
                                @endforeach
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800">
                                Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $p->tgl_pesanan->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusClass = match($p->status_pesanan) {
                                        'Pending' => 'bg-yellow-100 text-yellow-700',
                                        'Proses'  => 'bg-orange-100 text-orange-700',
                                        'Selesai' => 'bg-green-100 text-green-700',
                                        default   => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ $p->status_pesanan }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
