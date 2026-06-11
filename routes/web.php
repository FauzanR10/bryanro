<?php

/**
 * routes/web.php
 * ===============
 * File ini mendefinisikan semua URL (route) yang bisa diakses di aplikasi BryanRO.
 * Setiap route menghubungkan URL ke Controller yang menangani logikanya.
 *
 * Struktur route:
 * - Route tanpa middleware  → bisa diakses siapa saja (halaman login)
 * - Route dengan middleware 'cek.login' → hanya bisa diakses setelah login
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\LaporanController;

// ─────────────────────────────────────────────
// ROUTE PUBLIK (tanpa login)
// ─────────────────────────────────────────────

// Halaman utama → redirect ke login
Route::get('/', fn() => redirect()->route('login'));

// Login: tampilkan form login
Route::get('/login', [AuthController::class, 'tampilLogin'])->name('login');

// Login: proses form login (POST dari form)
Route::post('/login', [AuthController::class, 'prosesLogin'])->name('login.proses');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ─────────────────────────────────────────────
// ROUTE YANG BUTUH LOGIN (middleware cek.login)
// ─────────────────────────────────────────────
// Semua route di dalam group ini otomatis dicek loginnya.
// Jika belum login → diarahkan ke halaman login.

Route::middleware('cek.login')->group(function () {

    // ── DASHBOARD ──────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── PELANGGAN ──────────────────────────────
    // Tampilkan daftar pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    // Simpan pelanggan baru (dari modal form)
    Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    // Update data pelanggan
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
    // Hapus pelanggan
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');

    // ── PRODUK ─────────────────────────────────
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

    // ── PESANAN ────────────────────────────────
    // Tampilkan daftar pesanan (bisa difilter by status via query string ?status=Pending)
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    // Tambah pesanan baru → otomatis kirim WA ke pelanggan
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    // Update status pesanan → otomatis kirim WA ke pelanggan
    Route::put('/pesanan/{id}/status', [PesananController::class, 'updateStatus'])->name('pesanan.updateStatus');
    // Hapus pesanan
    Route::delete('/pesanan/{id}', [PesananController::class, 'destroy'])->name('pesanan.destroy');
    // API: ambil data pelanggan (untuk isi form pesanan secara otomatis via JS)
    Route::get('/api/pelanggan/{id}', [PesananController::class, 'getPelanggan'])->name('api.pelanggan');

    // ── LAPORAN ────────────────────────────────
    // Tampilkan laporan penjualan (bisa difilter by tanggal via query string)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});
