{{--
    HALAMAN DATA PRODUK
    ===================
    Menampilkan daftar produk yang dijual depot BryanRO.
    Fitur: Tambah produk baru, Edit harga, Hapus produk.
    Menampilkan statistik: total terjual dan total pendapatan per produk.
--}}
@extends('layouts.app')
@section('title', 'Data Produk')

@section('konten')
<div class="px-6 py-5 space-y-4">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Data Produk</h1>
            <p class="text-xs text-gray-500">Kelola produk dan harga layanan depot BryanRO</p>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Produk
        </button>
    </div>

    {{-- ── Info Banner ─────────────────────────────────── --}}
    <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4 flex gap-3">
        <div class="w-9 h-9 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0 text-lg">💡</div>
        <div>
            <p class="text-sm font-semibold text-cyan-800">Catatan Sistem</p>
            <p class="text-xs text-cyan-600 mt-0.5">
                Saat ini BryanRO hanya melayani satu jenis produk (Galon Isi Ulang).
                Tabel produk ini dirancang skalabel — produk baru dapat ditambahkan jika depot mengembangkan variasi layanan.
            </p>
        </div>
    </div>

    {{-- ── Tabel Produk ─────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 text-sm">Daftar Produk</h2>
            <span class="text-xs text-gray-400">{{ $produkDenganStats->count() }} produk terdaftar</span>
        </div>

        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ID</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Nama Produk</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Harga Satuan</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Total Terjual</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Total Pendapatan</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($produkDenganStats as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 font-mono text-gray-400">#{{ str_pad($p->id_produk, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center text-xl">🫙</div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $p->nama_produk }}</p>
                                    <p class="text-gray-400">Produk utama depot</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <p class="font-bold text-gray-800">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>
                            <p class="text-gray-400">per galon</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-bold">
                                {{ $p->total_terjual }} galon
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right font-bold text-green-700">
                            Rp {{ number_format($p->total_pendapatan, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="bukaModalEdit({{ $p->id_produk }}, '{{ addslashes($p->nama_produk) }}', {{ $p->harga }})"
                                        class="flex items-center gap-1 px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg font-medium transition-colors">
                                    ✏️ Edit
                                </button>
                                <form action="{{ route('produk.destroy', $p->id_produk) }}" method="POST"
                                      onsubmit="return confirm('Hapus produk {{ $p->nama_produk }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            Belum ada produk. Klik "Tambah Produk" untuk mulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ════════════════ MODAL TAMBAH ════════════════ --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Tambah Produk Baru</h3>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form action="{{ route('produk.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="nama_produk" placeholder="Contoh: Galon Isi Ulang"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500"
                       maxlength="40" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="harga" placeholder="Contoh: 5000" min="0" step="500"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500"
                       required>
                <p class="text-xs text-gray-400 mt-1">Harga per galon dalam Rupiah.</p>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2 rounded-lg text-xs">Batal</button>
                <button type="submit" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 rounded-lg text-xs">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════ MODAL EDIT ════════════════ --}}
<div id="modalEdit" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Edit Produk</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form id="formEdit" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" id="editNama" name="nama_produk" maxlength="40"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                <input type="number" id="editHarga" name="harga" min="0" step="500"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <p class="text-xs text-yellow-700">⚠️ Perubahan harga berlaku untuk pesanan yang dibuat setelah ini.</p>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2 rounded-lg text-xs">Batal</button>
                <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 rounded-lg text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function bukaModalEdit(id, nama, harga) {
    document.getElementById('editNama').value  = nama;
    document.getElementById('editHarga').value = harga;
    document.getElementById('formEdit').action = '/produk/' + id;
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>
@endpush

@endsection
