<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pelanggan;

/**
 * LaporanController
 * =================
 * Controller untuk halaman Laporan Penjualan.
 *
 * Laporan menampilkan:
 * - Filter berdasarkan rentang tanggal (dari - sampai)
 * - Statistik total (pesanan, pendapatan, pelanggan aktif, galon terjual)
 * - Tabel detail transaksi sesuai filter
 * - Rekapitulasi per status pesanan
 */
class LaporanController extends Controller
{
    /**
     * Tampilkan laporan penjualan
     * Route: GET /laporan
     *
     * Query param opsional:
     * - ?dari=YYYY-MM-DD   → filter mulai tanggal
     * - ?sampai=YYYY-MM-DD → filter sampai tanggal
     */
    public function index(Request $request)
    {
        // ── Tentukan rentang tanggal ───────────────────────────
        // Default: bulan ini (dari tanggal 1 hingga hari ini)
        $dari   = $request->filled('dari')   ? $request->dari   : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        // ── Query pesanan berdasarkan rentang tanggal ──────────
        $query = Pesanan::with(['pelanggan', 'detail.produk'])
            ->whereBetween('tgl_pesanan', [$dari, $sampai])
            ->orderBy('tgl_pesanan', 'desc');

        $pesananList = $query->get();

        // ── Statistik rangkuman ────────────────────────────────

        // Total pendapatan dari pesanan yang sudah Selesai
        $totalPendapatan = $pesananList->where('status_pesanan', 'Selesai')->sum('total_harga');

        // Total semua pesanan dalam periode
        $totalPesanan = $pesananList->count();

        // Total galon terjual (dari detail_pesanan)
        $idPesanan = $pesananList->pluck('id_pesanan');
        $totalGalon = DetailPesanan::whereIn('id_pesanan', $idPesanan)->sum('jumlah');

        // Jumlah pelanggan unik yang pesan dalam periode ini
        $pelangganAktif = $pesananList->pluck('id_pelanggan')->unique()->count();

        // Hitung pesanan per status
        $statusCount = [
            'Pending' => $pesananList->where('status_pesanan', 'Pending')->count(),
            'Proses'  => $pesananList->where('status_pesanan', 'Proses')->count(),
            'Selesai' => $pesananList->where('status_pesanan', 'Selesai')->count(),
        ];

        // ── Data chart harian ──────────────────────────────────
        // Kelompokkan pesanan per hari untuk ditampilkan sebagai tabel tren
        $dataHarian = $pesananList->groupBy(function ($p) {
            return $p->tgl_pesanan->format('d M Y');
        })->map(function ($group) {
            return [
                'jumlah_pesanan' => $group->count(),
                'pendapatan'     => $group->where('status_pesanan', 'Selesai')->sum('total_harga'),
            ];
        });

        return view('laporan.index', compact(
            'pesananList',
            'dari',
            'sampai',
            'totalPendapatan',
            'totalPesanan',
            'totalGalon',
            'pelangganAktif',
            'statusCount',
            'dataHarian'
        ));
    }
}
