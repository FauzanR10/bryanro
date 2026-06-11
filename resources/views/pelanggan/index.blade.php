{{--
    HALAMAN DAFTAR PELANGGAN
    ========================
    Menampilkan semua pelanggan yang terdaftar di sistem BryanRO.
    Fitur:
    - Pencarian berdasarkan nama / nomor WA / alamat
    - Tambah pelanggan baru (via modal form)
    - Edit data pelanggan (via modal form)
    - Hapus pelanggan (dengan konfirmasi)
    - Pagination 10 data per halaman
--}}
@extends('layouts.app')
@section('title', 'Data Pelanggan')

@section('konten')
<div class="px-6 py-5 space-y-4">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Data Pelanggan</h1>
            <p class="text-xs text-gray-500">Kelola data pelanggan depot BryanRO</p>
        </div>
        {{-- Tombol buka modal Tambah Pelanggan --}}
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pelanggan
        </button>
    </div>

    {{-- ── Kartu Statistik ─────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-xs text-gray-500">Total Pelanggan</p>
            <p class="text-2xl font-bold text-blue-700">{{ $totalPelanggan }}</p>
            <p class="text-xs text-gray-400">terdaftar di sistem</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-xs text-gray-500">Hasil Pencarian</p>
            <p class="text-2xl font-bold text-green-700">{{ $pelanggan->total() }}</p>
            <p class="text-xs text-gray-400">pelanggan ditemukan</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
            <p class="text-xs text-gray-500">Halaman</p>
            <p class="text-2xl font-bold text-purple-700">{{ $pelanggan->currentPage() }}/{{ $pelanggan->lastPage() }}</p>
            <p class="text-xs text-gray-400">10 data per halaman</p>
        </div>
    </div>

    {{-- ── Form Pencarian ───────────────────────────────── --}}
    {{-- GET /pelanggan?cari=xxx — reload halaman dengan hasil filter --}}
    <form action="{{ route('pelanggan.index') }}" method="GET" class="flex gap-2">
        <div class="relative flex-1">
            <input type="text" name="cari" value="{{ request('cari') }}"
                   placeholder="Cari nama, nomor WA, atau alamat pelanggan..."
                   class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="absolute left-3 top-2.5 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700">Cari</button>
        @if(request('cari'))
            <a href="{{ route('pelanggan.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs rounded-lg hover:bg-gray-50">Reset</a>
        @endif
    </form>

    {{-- ── Tabel Daftar Pelanggan ──────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">No</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Nama Pelanggan</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Nomor WhatsApp</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Alamat</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pelanggan as $index => $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-400 font-mono">
                            {{ ($pelanggan->currentPage() - 1) * $pelanggan->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                {{-- Avatar inisial nama --}}
                                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-blue-700 text-xs font-bold uppercase">{{ substr($p->nama, 0, 1) }}</span>
                                </div>
                                <span class="font-medium text-gray-800">{{ $p->nama }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $p->no_wa }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $p->alamat }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Tombol Edit: buka modal edit dan isi data via JavaScript --}}
                                <button onclick="bukaModalEdit({{ $p->id_pelanggan }}, '{{ addslashes($p->nama) }}', '{{ $p->no_wa }}', '{{ addslashes($p->alamat) }}')"
                                        class="px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg font-medium transition-colors">
                                    Edit
                                </button>
                                {{-- Tombol Hapus: form DELETE (Laravel menggunakan method spoofing) --}}
                                <form action="{{ route('pelanggan.destroy', $p->id_pelanggan) }}" method="POST"
                                      onsubmit="return confirm('Hapus pelanggan {{ $p->nama }}? Aksi ini tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE') {{-- Spoof method DELETE karena HTML hanya support GET & POST --}}
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
                        <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                            {{ request('cari') ? 'Tidak ada pelanggan yang cocok dengan pencarian "' . request('cari') . '".' : 'Belum ada data pelanggan. Klik "Tambah Pelanggan" untuk mulai.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination links --}}
        @if($pelanggan->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $pelanggan->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ════════════════════════════════════
     MODAL: TAMBAH PELANGGAN
     ════════════════════════════════════
     Tersembunyi secara default (class "hidden").
     Muncul saat tombol "Tambah Pelanggan" diklik.
--}}
<div id="modalTambah" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Tambah Pelanggan Baru</h3>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form POST ke /pelanggan --}}
        <form action="{{ route('pelanggan.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" placeholder="Contoh: Budi Santoso"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       maxlength="70" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                <input type="text" name="no_wa" placeholder="Contoh: 081234567890"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       maxlength="20" required>
                <p class="text-xs text-gray-400 mt-1">Nomor ini akan dipakai untuk mengirim notifikasi WhatsApp.</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="alamat" rows="3" placeholder="Contoh: Jl. Merpati No. 12, Bogor"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          maxlength="150" required></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2 rounded-lg text-xs hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg text-xs">
                    Simpan Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════
     MODAL: EDIT PELANGGAN
     ════════════════════════════════════
     Data diisi via JavaScript saat tombol Edit diklik.
--}}
<div id="modalEdit" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Edit Data Pelanggan</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{--
            Form PUT ke /pelanggan/{id}
            Karena HTML hanya support GET & POST, Laravel pakai @method('PUT')
            untuk memberi tahu framework bahwa ini adalah request PUT.
        --}}
        <form id="formEdit" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="editNama" name="nama"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       maxlength="70" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                <input type="text" id="editNoWa" name="no_wa"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       maxlength="20" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea id="editAlamat" name="alamat" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          maxlength="150" required></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2 rounded-lg text-xs hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 rounded-lg text-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
/**
 * Fungsi untuk membuka modal Edit Pelanggan
 * dan mengisi form dengan data pelanggan yang dipilih.
 *
 * Dipanggil oleh tombol Edit di setiap baris tabel.
 */
function bukaModalEdit(id, nama, noWa, alamat) {
    // Isi field form dengan data pelanggan
    document.getElementById('editNama').value  = nama;
    document.getElementById('editNoWa').value  = noWa;
    document.getElementById('editAlamat').value = alamat;

    // Set action form ke URL yang sesuai: PUT /pelanggan/{id}
    document.getElementById('formEdit').action = '/pelanggan/' + id;

    // Tampilkan modal (hapus class 'hidden')
    document.getElementById('modalEdit').classList.remove('hidden');
}

// Tutup modal jika klik di area luar (background gelap)
document.getElementById('modalTambah').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.getElementById('modalEdit').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endpush

@endsection
