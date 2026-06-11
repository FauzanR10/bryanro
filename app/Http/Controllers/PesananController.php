<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\DetailPesanan;
use App\Models\NotifikasiWa;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;

/**
 * PesananController
 * =================
 * Controller untuk halaman Daftar Pesanan.
 *
 * Ini adalah controller terpenting karena menghubungkan:
 * 1. Pembuatan pesanan → simpan ke DB → kirim WA ke pelanggan
 * 2. Update status → update DB → kirim WA notifikasi baru
 *
 * Alur pesanan:
 * Admin input → Pending → (Admin proses) → Proses → (Selesai diantar) → Selesai
 * Setiap perubahan status → WA otomatis terkirim ke pelanggan
 */
class PesananController extends Controller
{
    // Inject WhatsAppService melalui constructor (Dependency Injection)
    // Laravel otomatis menyediakan instance ini
    public function __construct(private WhatsAppService $wa) {}

    /**
     * Tampilkan daftar pesanan
     * Route: GET /pesanan
     *
     * Query param opsional:
     * - ?status=Pending|Proses|Selesai  → filter berdasarkan status
     * - ?cari=xxx                        → cari nama pelanggan
     */
    public function index(Request $request)
    {
        $query = Pesanan::with(['pelanggan', 'detail.produk'])
            ->orderBy('id_pesanan', 'desc'); // Pesanan terbaru di atas

        // Filter berdasarkan status pesanan
        if ($request->filled('status') && in_array($request->status, ['Pending', 'Proses', 'Selesai'])) {
            $query->where('status_pesanan', $request->status);
        }

        // Cari berdasarkan nama pelanggan
        if ($request->filled('cari')) {
            $kata = $request->cari;
            $query->whereHas('pelanggan', function ($q) use ($kata) {
                $q->where('nama', 'like', "%{$kata}%")
                  ->orWhere('no_wa', 'like', "%{$kata}%");
            });
        }

        $pesanan = $query->paginate(10)->withQueryString();

        // Hitung jumlah pesanan per status untuk badge di tab filter
        $hitungStatus = [
            'semua'   => Pesanan::count(),
            'Pending' => Pesanan::where('status_pesanan', 'Pending')->count(),
            'Proses'  => Pesanan::where('status_pesanan', 'Proses')->count(),
            'Selesai' => Pesanan::where('status_pesanan', 'Selesai')->count(),
        ];

        // Data untuk dropdown di form tambah pesanan
        $pelangganList = Pelanggan::orderBy('nama')->get();
        $produkList    = Produk::all();

        return view('pesanan.index', compact('pesanan', 'hitungStatus', 'pelangganList', 'produkList'));
    }

    /**
     * Simpan pesanan baru ke database
     * Route: POST /pesanan
     *
     * Setelah disimpan → kirim WA konfirmasi ke pelanggan secara otomatis
     */
    public function store(Request $request)
    {
        // Validasi input form
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_produk'    => 'required|exists:produk,id_produk',
            'jumlah'       => 'required|integer|min:1',
            'tgl_pesanan'  => 'required|date',
        ], [
            'id_pelanggan.required' => 'Pelanggan wajib dipilih.',
            'id_pelanggan.exists'   => 'Pelanggan tidak ditemukan di database.',
            'id_produk.required'    => 'Produk wajib dipilih.',
            'id_produk.exists'      => 'Produk tidak ditemukan di database.',
            'jumlah.required'       => 'Jumlah wajib diisi.',
            'jumlah.min'            => 'Jumlah minimal 1.',
            'tgl_pesanan.required'  => 'Tanggal pesanan wajib diisi.',
        ]);

        // Ambil data produk untuk kalkulasi harga
        $produk = Produk::findOrFail($request->id_produk);

        // Hitung subtotal: jumlah × harga satuan
        $subtotal    = $request->jumlah * $produk->harga;
        $totalHarga  = $subtotal; // Untuk saat ini 1 produk per pesanan

        // ── Gunakan Transaction DB ──────────────────────────────
        // Transaction memastikan SEMUA query berhasil, atau SEMUANYA dibatalkan.
        // Contoh: jika pesanan berhasil dibuat tapi detail_pesanan gagal → pesanan dibatalkan.
        DB::beginTransaction();
        try {
            // 1. Buat record pesanan
            $pesanan = Pesanan::create([
                'id_admin'       => session('admin_id'),    // Dari session login
                'id_pelanggan'   => $request->id_pelanggan,
                'tgl_pesanan'    => $request->tgl_pesanan,
                'total_harga'    => $totalHarga,
                'status_pesanan' => 'Pending',               // Status awal selalu Pending
            ]);

            // 2. Buat record detail_pesanan (produk yang dipesan)
            DetailPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'id_produk'  => $request->id_produk,
                'jumlah'     => $request->jumlah,
                'subtotal'   => $subtotal,
            ]);

            DB::commit(); // Semua berhasil → simpan ke database

        } catch (\Exception $e) {
            DB::rollBack(); // Ada error → batalkan semua
            return redirect()->route('pesanan.index')
                ->with('error', 'Gagal menyimpan pesanan: ' . $e->getMessage());
        }

        // ── Kirim WhatsApp Gateway ──────────────────────────────
        // Dilakukan SETELAH transaction berhasil
        $pelanggan = Pelanggan::findOrFail($request->id_pelanggan);

        $isiPesan = $this->wa->templatePesananBaru(
            $pelanggan->nama,
            $pesanan->id_pesanan,
            $produk->nama_produk,
            $request->jumlah,
            $totalHarga
        );

        $hasilWa = $this->wa->kirimPesan($pelanggan->no_wa, $isiPesan);

        // Catat riwayat pengiriman WA ke tabel notifikasi_wa
        NotifikasiWa::create([
            'id_pesanan'   => $pesanan->id_pesanan,
            'isi_pesan'    => $isiPesan,
            'tgl_kirim'    => now()->toDateString(),
            'status_kirim' => $hasilWa['success'] ? 'Selesai' : 'Proses', // Proses = gagal/pending
        ]);

        $pesanFlash = 'Pesanan #' . $pesanan->id_pesanan . ' berhasil dibuat.';
        $pesanFlash .= $hasilWa['success']
            ? ' Notifikasi WhatsApp terkirim ke ' . $pelanggan->no_wa . '.'
            : ' (Catatan: ' . $hasilWa['message'] . ')';

        return redirect()->route('pesanan.index')->with('success', $pesanFlash);
    }

    /**
     * Update status pesanan (Pending → Proses → Selesai)
     * Route: PUT /pesanan/{id}/status
     *
     * Setelah diupdate → kirim WA notifikasi ke pelanggan
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pesanan' => 'required|in:Pending,Proses,Selesai',
        ], [
            'status_pesanan.required' => 'Status wajib dipilih.',
            'status_pesanan.in'       => 'Status tidak valid. Pilih: Pending, Proses, atau Selesai.',
        ]);

        $pesanan = Pesanan::with('pelanggan')->findOrFail($id);
        $statusLama = $pesanan->status_pesanan;
        $statusBaru = $request->status_pesanan;

        // Update status di database
        $pesanan->update(['status_pesanan' => $statusBaru]);

        // ── Kirim WA jika status berubah ───────────────────────
        if ($statusLama !== $statusBaru) {
            $isiPesan = $this->wa->templateUpdateStatus(
                $pesanan->pelanggan->nama,
                $pesanan->id_pesanan,
                $statusBaru
            );

            $hasilWa = $this->wa->kirimPesan($pesanan->pelanggan->no_wa, $isiPesan);

            // Catat riwayat pengiriman WA
            NotifikasiWa::create([
                'id_pesanan'   => $pesanan->id_pesanan,
                'isi_pesan'    => $isiPesan,
                'tgl_kirim'    => now()->toDateString(),
                'status_kirim' => $hasilWa['success'] ? 'Selesai' : 'Proses',
            ]);

            $pesanWa = $hasilWa['success']
                ? ' Notifikasi WhatsApp berhasil dikirim ke pelanggan.'
                : ' (WA: ' . $hasilWa['message'] . ')';
        } else {
            $pesanWa = '';
        }

        return redirect()->route('pesanan.index')
            ->with('success', 'Status pesanan #' . $id . ' berhasil diubah ke ' . $statusBaru . '.' . $pesanWa);
    }

    /**
     * Hapus pesanan
     * Route: DELETE /pesanan/{id}
     */
    public function destroy($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        // Hapus detail_pesanan dan notifikasi_wa terkait terlebih dahulu (karena ada FK)
        $pesanan->notifikasi()->delete();
        $pesanan->detail()->delete();
        $pesanan->delete();

        return redirect()->route('pesanan.index')
            ->with('success', 'Pesanan #' . $id . ' berhasil dihapus.');
    }

    /**
     * API: Ambil data pelanggan berdasarkan ID
     * Route: GET /api/pelanggan/{id}
     *
     * Digunakan oleh JavaScript di form tambah pesanan:
     * Saat admin memilih pelanggan dari dropdown → tampilkan nomor WA otomatis
     */
    public function getPelanggan($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        // Kembalikan data sebagai JSON
        return response()->json([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'nama'         => $pelanggan->nama,
            'no_wa'        => $pelanggan->no_wa,
            'alamat'       => $pelanggan->alamat,
        ]);
    }
}
