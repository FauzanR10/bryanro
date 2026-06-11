{{--
    HALAMAN DAFTAR PESANAN
    ======================
    Halaman inti sistem BryanRO — tempat admin mengelola semua transaksi.

    Fitur:
    - Tab filter: Semua / Pending / Proses / Selesai
    - Pencarian berdasarkan nama pelanggan / nomor WA
    - Tambah pesanan baru (modal) → otomatis kirim WA konfirmasi
    - Update status pesanan (modal) → otomatis kirim WA notifikasi
    - Hapus pesanan
    - Indikator WhatsApp Gateway aktif/tidaknya
--}}
@extends('layouts.app')
@section('title', 'Daftar Pesanan')

@section('konten')
<div class="px-6 py-5 space-y-4">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Daftar Pesanan</h1>
            <p class="text-xs text-gray-500">Pantau dan kelola semua transaksi pesanan pelanggan</p>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pesanan
        </button>
    </div>

    {{-- ── Tab Filter Status + Pencarian ──────────────────── --}}
    <div class="flex items-center gap-3 flex-wrap">

        {{-- Tab status filter --}}
        <div class="flex gap-1 flex-wrap">
            @php
                $statusList = [
                    'semua'   => ['label' => 'Semua',   'count' => $hitungStatus['semua'],   'color' => 'blue'],
                    'Pending' => ['label' => 'Pending',  'count' => $hitungStatus['Pending'], 'color' => 'yellow'],
                    'Proses'  => ['label' => 'Proses',   'count' => $hitungStatus['Proses'],  'color' => 'orange'],
                    'Selesai' => ['label' => 'Selesai',  'count' => $hitungStatus['Selesai'], 'color' => 'green'],
                ];
                $statusAktif = request('status', 'semua');
            @endphp

            @foreach($statusList as $key => $info)
                @php
                    $isAktif = ($statusAktif === $key) || ($key === 'semua' && !request('status'));
                    $href = $key === 'semua'
                        ? route('pesanan.index')
                        : route('pesanan.index', ['status' => $key]);
                @endphp
                <a href="{{ $href }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors
                          {{ $isAktif
                              ? 'bg-blue-600 text-white border-blue-600'
                              : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    {{ $info['label'] }}
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs
                                 {{ $isAktif ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600' }}">
                        {{ $info['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Pencarian + Indikator WA --}}
        <div class="flex gap-2 ml-auto items-center">
            {{-- Indikator status WhatsApp Gateway --}}
            @php $waToken = config('services.fonnte.token'); @endphp
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border
                        {{ $waToken && $waToken !== 'ISI_TOKEN_FONNTE_DISINI'
                           ? 'bg-green-50 border-green-200'
                           : 'bg-gray-50 border-gray-200' }}">
                <span class="w-2 h-2 rounded-full {{ $waToken && $waToken !== 'ISI_TOKEN_FONNTE_DISINI' ? 'bg-green-500 animate-pulse' : 'bg-gray-300' }}"></span>
                <span class="text-xs font-medium {{ $waToken && $waToken !== 'ISI_TOKEN_FONNTE_DISINI' ? 'text-green-700' : 'text-gray-500' }}">
                    WhatsApp Gateway {{ $waToken && $waToken !== 'ISI_TOKEN_FONNTE_DISINI' ? 'Aktif' : 'Belum Dikonfigurasi' }}
                </span>
            </div>

            {{-- Form cari --}}
            <form action="{{ route('pesanan.index') }}" method="GET" class="flex gap-1">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}"
                           placeholder="Cari nama / nomor WA..."
                           class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 w-52">
                    <svg class="absolute left-2.5 top-2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Cari</button>
            </form>
        </div>
    </div>

    {{-- ── Tabel Daftar Pesanan ─────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            Menampilkan <strong>{{ $pesanan->count() }}</strong> dari <strong>{{ $pesanan->total() }}</strong> pesanan
        </div>

        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">No. Pesanan</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Pelanggan</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">WhatsApp</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Produk</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Jumlah</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Total</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pesanan as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <span class="font-mono font-bold text-blue-600">
                                #{{ str_pad($p->id_pesanan, 3, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-blue-700 text-xs font-bold uppercase">
                                        {{ substr($p->pelanggan->nama ?? 'X', 0, 1) }}
                                    </span>
                                </div>
                                <span class="font-medium text-gray-800">{{ $p->pelanggan->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $p->pelanggan->no_wa ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @foreach($p->detail as $d)
                                {{ $d->produk->nama_produk ?? '-' }}
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-700">
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
                            <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                {{ $p->status_pesanan }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                {{-- Tombol Update Status --}}
                                <button onclick="bukaModalUpdate({{ $p->id_pesanan }}, '{{ $p->status_pesanan }}', '{{ addslashes($p->pelanggan->nama ?? '') }}')"
                                        class="px-2.5 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg font-medium transition-colors">
                                    Update
                                </button>
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('pesanan.destroy', $p->id_pesanan) }}" method="POST"
                                      onsubmit="return confirm('Hapus pesanan #{{ str_pad($p->id_pesanan, 3, '0', STR_PAD_LEFT) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-2.5 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-10 text-center text-gray-400">
                            {{ request('status') ? 'Tidak ada pesanan berstatus "' . request('status') . '".' : 'Belum ada pesanan. Klik "Tambah Pesanan" untuk mulai.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Total & Pagination --}}
        <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-500">
                Total nilai: <strong>Rp {{ number_format($pesanan->sum('total_harga'), 0, ',', '.') }}</strong>
            </span>
            {{ $pesanan->links() }}
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════
     MODAL: TAMBAH PESANAN BARU
     Setelah disimpan → sistem otomatis kirim WA
     ════════════════════════════════════════════ --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-800">Tambah Pesanan Baru</h3>
                <p class="text-xs text-gray-500 mt-0.5">Sistem akan otomatis kirim konfirmasi ke WhatsApp pelanggan</p>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <form action="{{ route('pesanan.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Pilih Pelanggan --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Pilih Pelanggan <span class="text-red-500">*</span>
                </label>
                <select name="id_pelanggan" id="selectPelanggan" onchange="isiInfoPelanggan(this.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Pilih pelanggan --</option>
                    @foreach($pelangganList as $pl)
                        <option value="{{ $pl->id_pelanggan }}">{{ $pl->nama }} — {{ $pl->no_wa }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Info WA pelanggan (diisi otomatis via JS) --}}
            <div id="infoWa" class="hidden px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-xs text-green-700">
                    💬 Notifikasi WA akan dikirim ke: <strong id="namaWa"></strong> (<span id="noWa"></span>)
                </p>
            </div>

            {{-- Pilih Produk --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Produk <span class="text-red-500">*</span>
                </label>
                <select name="id_produk" id="selectProduk" onchange="hitungTotal()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Pilih produk --</option>
                    @foreach($produkList as $pr)
                        <option value="{{ $pr->id_produk }}" data-harga="{{ $pr->harga }}">
                            {{ $pr->nama_produk }} — Rp {{ number_format($pr->harga, 0, ',', '.') }}/galon
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Jumlah --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Jumlah Galon <span class="text-red-500">*</span>
                </label>
                <input type="number" name="jumlah" id="inputJumlah" min="1" value="1" onchange="hitungTotal()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            {{-- Preview Total (dihitung otomatis oleh JS) --}}
            <div id="previewTotal" class="hidden px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-700">
                    Total: <strong id="tampilTotal">Rp 0</strong>
                </p>
            </div>

            {{-- Tanggal Pesanan --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Tanggal Pesanan <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tgl_pesanan" value="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2 rounded-lg text-xs">Batal</button>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg text-xs">
                    💬 Simpan & Kirim WA
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════
     MODAL: UPDATE STATUS PESANAN
     Setelah diupdate → sistem otomatis kirim WA
     ════════════════════════════════════════════ --}}
<div id="modalUpdate" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-800">Update Status Pesanan</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="labelNamaPelanggan">Pelanggan: —</p>
            </div>
            <button onclick="document.getElementById('modalUpdate').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <form id="formUpdate" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Pilih status baru --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Status Pesanan</label>
                <div class="space-y-2">
                    @foreach(['Pending' => ['⏳', 'Pesanan diterima, menunggu diproses', 'yellow'], 'Proses' => ['🚚', 'Pesanan sedang dalam pengiriman', 'orange'], 'Selesai' => ['✅', 'Pesanan telah diterima pelanggan', 'green']] as $status => [$emoji, $desc, $color])
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-400 has-[:checked]:bg-blue-50">
                            <input type="radio" name="status_pesanan" value="{{ $status }}"
                                   class="text-blue-600">
                            <span class="text-sm">{{ $emoji }}</span>
                            <div>
                                <p class="text-xs font-bold text-gray-800">{{ $status }}</p>
                                <p class="text-xs text-gray-500">{{ $desc }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Info WA --}}
            <div class="px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-xs text-green-700">
                    💬 Notifikasi WhatsApp otomatis akan dikirim saat status berubah.
                </p>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modalUpdate').classList.add('hidden')"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2 rounded-lg text-xs">Batal</button>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg text-xs">
                    💬 Simpan & Kirim WA
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
/**
 * Buka modal update status dan set action form ke URL yang benar
 */
function bukaModalUpdate(idPesanan, statusSaatIni, namaPelanggan) {
    // Set action form: PUT /pesanan/{id}/status
    document.getElementById('formUpdate').action = '/pesanan/' + idPesanan + '/status';

    // Tampilkan nama pelanggan di header modal
    document.getElementById('labelNamaPelanggan').textContent = 'Pesanan #' + String(idPesanan).padStart(3, '0') + ' — ' + namaPelanggan;

    // Pilih radio button sesuai status saat ini
    const radios = document.querySelectorAll('input[name="status_pesanan"]');
    radios.forEach(r => r.checked = (r.value === statusSaatIni));

    document.getElementById('modalUpdate').classList.remove('hidden');
}

/**
 * Isi info nomor WA pelanggan secara otomatis saat dropdown pelanggan dipilih
 * Memanggil API /api/pelanggan/{id} yang sudah dibuat di PesananController
 */
function isiInfoPelanggan(idPelanggan) {
    if (!idPelanggan) {
        document.getElementById('infoWa').classList.add('hidden');
        return;
    }

    // Fetch data pelanggan dari endpoint API Laravel
    fetch('/api/pelanggan/' + idPelanggan)
        .then(r => r.json())
        .then(data => {
            document.getElementById('namaWa').textContent = data.nama;
            document.getElementById('noWa').textContent   = data.no_wa;
            document.getElementById('infoWa').classList.remove('hidden');
        })
        .catch(() => document.getElementById('infoWa').classList.add('hidden'));
}

/**
 * Hitung dan tampilkan preview total harga secara real-time
 */
function hitungTotal() {
    const produkSelect = document.getElementById('selectProduk');
    const jumlah = parseInt(document.getElementById('inputJumlah').value) || 0;

    if (!produkSelect.value || jumlah <= 0) {
        document.getElementById('previewTotal').classList.add('hidden');
        return;
    }

    const harga = parseFloat(produkSelect.selectedOptions[0].dataset.harga) || 0;
    const total = harga * jumlah;

    // Format angka ke Rupiah: 10000 → Rp 10.000
    document.getElementById('tampilTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('previewTotal').classList.remove('hidden');
}

// Tutup modal saat klik background
['modalTambah', 'modalUpdate'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
});
</script>
@endpush
@endsection
