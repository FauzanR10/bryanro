{{--
    HALAMAN LAPORAN PENJUALAN
    =========================
    Menampilkan rekapitulasi penjualan dalam rentang tanggal tertentu.
    Fitur:
    - Filter rentang tanggal (dari - sampai)
    - 4 kartu statistik (pesanan, pendapatan, galon, pelanggan aktif)
    - Tabel detail semua pesanan dalam periode
    - Rekap per status pesanan
--}}
@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('konten')
<div class="px-6 py-5 space-y-4">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div>
        <h1 class="text-lg font-bold text-gray-800">Laporan Penjualan</h1>
        <p class="text-xs text-gray-500">Rekapitulasi penjualan depot BryanRO</p>
    </div>

    {{-- ── Form Filter Tanggal ─────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Filter Periode Laporan</h3>
        <form action="{{ route('laporan.index') }}" method="GET" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                Tampilkan Laporan
            </button>
            {{-- Reset ke bulan ini --}}
            <a href="{{ route('laporan.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-600 text-xs rounded-lg hover:bg-gray-50">
                Reset
            </a>
            {{-- Tombol cetak halaman ini --}}
            <button type="button" onclick="window.print()"
                    class="ml-auto px-4 py-2 border border-gray-300 text-gray-600 text-xs rounded-lg hover:bg-gray-50 flex items-center gap-1.5">
                🖨️ Cetak Laporan
            </button>
        </form>

        {{-- Info periode aktif --}}
        <div class="mt-3 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-xs text-blue-700">
                📅 Menampilkan laporan periode:
                <strong>{{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}</strong>
                s/d
                <strong>{{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</strong>
            </p>
        </div>
    </div>

    {{-- ── 4 Kartu Statistik ───────────────────────────── --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs text-gray-500">Total Pesanan</p>
                <span class="text-lg">📋</span>
            </div>
            <p class="text-2xl font-bold text-blue-700">{{ $totalPesanan }}</p>
            <p class="text-xs text-gray-400">dalam periode ini</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs text-gray-500">Total Pendapatan</p>
                <span class="text-lg">💰</span>
            </div>
            <p class="text-xl font-bold text-green-700">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400">dari pesanan Selesai</p>
        </div>
        <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs text-gray-500">Galon Terjual</p>
                <span class="text-lg">🫙</span>
            </div>
            <p class="text-2xl font-bold text-cyan-700">{{ $totalGalon }}</p>
            <p class="text-xs text-gray-400">galon isi ulang</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs text-gray-500">Pelanggan Aktif</p>
                <span class="text-lg">👤</span>
            </div>
            <p class="text-2xl font-bold text-purple-700">{{ $pelangganAktif }}</p>
            <p class="text-xs text-gray-400">pelanggan memesan</p>
        </div>
    </div>

    {{-- ── Rekap Status ─────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white border border-yellow-200 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Pending</p>
                <p class="text-xl font-bold text-yellow-600">{{ $statusCount['Pending'] }}</p>
            </div>
            <span class="text-2xl">⏳</span>
        </div>
        <div class="bg-white border border-orange-200 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Proses</p>
                <p class="text-xl font-bold text-orange-600">{{ $statusCount['Proses'] }}</p>
            </div>
            <span class="text-2xl">🚚</span>
        </div>
        <div class="bg-white border border-green-200 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Selesai</p>
                <p class="text-xl font-bold text-green-600">{{ $statusCount['Selesai'] }}</p>
            </div>
            <span class="text-2xl">✅</span>
        </div>
    </div>

    {{-- ── Tabel Detail Laporan ─────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 text-sm">Detail Transaksi</h2>
            <span class="text-xs text-gray-400">{{ $pesananList->count() }} transaksi</span>
        </div>

        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">No. Pesanan</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Pelanggan</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Produk</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Jumlah</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Total</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pesananList as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono font-bold text-blue-600">
                            #{{ str_pad($p->id_pesanan, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $p->pelanggan->nama ?? '-' }}
                            <p class="text-gray-400 font-normal">{{ $p->pelanggan->no_wa ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            @foreach($p->detail as $d)
                                {{ $d->produk->nama_produk ?? '-' }}
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-center font-semibold">
                            @foreach($p->detail as $d)
                                {{ $d->jumlah }}x
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">
                            Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $p->tgl_pesanan->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $cls = match($p->status_pesanan) {
                                    'Pending' => 'bg-yellow-100 text-yellow-700',
                                    'Proses'  => 'bg-orange-100 text-orange-700',
                                    'Selesai' => 'bg-green-100 text-green-700',
                                    default   => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-1 rounded-full font-semibold {{ $cls }}">
                                {{ $p->status_pesanan }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                            Tidak ada transaksi dalam periode
                            {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} —
                            {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- Baris total --}}
            @if($pesananList->isNotEmpty())
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 border-gray-200 font-bold text-xs">
                        <td colspan="4" class="px-5 py-3 text-gray-700">Total Keseluruhan</td>
                        <td class="px-4 py-3 text-right text-green-700">
                            Rp {{ number_format($pesananList->sum('total_harga'), 0, ',', '.') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

</div>
@endsection
