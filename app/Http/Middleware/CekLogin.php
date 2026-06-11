<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware CekLogin
 * ===================
 * Middleware ini berjalan SETIAP KALI ada request ke route yang dilindungi.
 * Tugasnya: mengecek apakah admin sudah login atau belum.
 *
 * Cara kerja:
 * - Jika session 'admin_id' ada → admin sudah login → lanjutkan request
 * - Jika session 'admin_id' tidak ada → belum login → redirect ke halaman login
 *
 * Middleware ini didaftarkan di app/Http/Kernel.php dengan alias 'cek.login'
 */
class CekLogin
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah ada session login (session dibuat saat login berhasil)
        if (!session()->has('admin_id')) {
            // Belum login → arahkan ke halaman login dengan pesan
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Sudah login → lanjutkan ke controller yang dituju
        return $next($request);
    }
}
