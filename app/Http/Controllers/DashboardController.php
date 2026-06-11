<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\NotifikasiWa;

/**
 * DashboardController
 * ===================
 * Controller untuk halaman Dashboard Admin.
 *
 * Dashboard menampilkan:
 * - 4 kartu statistik (total pelanggan, pesanan, pendapatan bulan ini, WA terkirim)
 * - Daftar 5 pesanan terbaru
 * - Pesanan berdasarkan status (Pending / Proses / Selesai)
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard
     * Route: GET /dashboard
     */
    public function index()
    {
        // ── Statistik Utama ────────────────────────────────────

        // Total pelanggan yang terdaftar
        $totalPelanggan = Pelanggan::count();

        // Total semua pesanan
        $totalPesanan = Pesanan::count();

        // Total pendapatan bulan ini (berdasarkan tgl_pesanan)
        $pendapatanBulanIni = Pesanan::whereMonth('tgl_pesanan', now()->month)
            ->whereYear('tgl_pesanan', now()->year)
            ->sum('total_harga');

        // Total pesan WhatsApp yang berhasil dikirim
        $totalWaTerkirim = NotifikasiWa::where('status_kirim', 'Selesai')->count();

        // ── Hitung pesanan berdasarkan status ─────────────────
        $pesananPending  = Pesanan::where('status_pesanan', 'Pending')->count();
        $pesananProses   = Pesanan::where('status_pesanan', 'Proses')->count();
        $pesananSelesai  = Pesanan::where('status_pesanan', 'Selesai')->count();

        // ── 5 Pesanan Terbaru ──────────────────────────────────
        // Gunakan with() untuk eager loading (ambil data pelanggan sekaligus, hindari N+1 query)
        $pesananTerbaru = Pesanan::with(['pelanggan', 'detail.produk'])
            ->orderBy('id_pesanan', 'desc')  // Terbaru di atas
            ->limit(5)
            ->get();

        // Kirim semua data ke view dashboard/index.blade.php
        return view('dashboard.index', compact(
            'totalPelanggan',
            'totalPesanan',
            'pendapatanBulanIni',
            'totalWaTerkirim',
            'pesananPending',
            'pesananProses',
            'pesananSelesai',
            'pesananTerbaru'
        ));
    }
}
