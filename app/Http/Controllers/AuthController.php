<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

/**
 * AuthController
 * ==============
 * Controller untuk autentikasi admin (login & logout).
 *
 * Alur login:
 * 1. Admin buka /login → tampilForm()
 * 2. Admin isi username + password → submit form → prosesLogin()
 * 3. Jika benar → simpan id_admin dan username di session → redirect dashboard
 * 4. Jika salah → kembali ke form login dengan pesan error
 */
class AuthController extends Controller
{
    /**
     * Tampilkan halaman form login
     * Route: GET /login
     */
    public function tampilLogin()
    {
        // Jika sudah login, langsung ke dashboard (tidak perlu login lagi)
        if (session()->has('admin_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses form login yang dikirim admin
     * Route: POST /login
     */
    public function prosesLogin(Request $request)
    {
        // Validasi input: username dan password wajib diisi
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari admin berdasarkan username di database
        $admin = Admin::where('username', $request->username)->first();

        // Cek apakah admin ditemukan DAN password cocok
        // Hash::check() membandingkan password plain text dengan hash bcrypt di database
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            // Login gagal → kembalikan ke form dengan pesan error
            return back()->with('error', 'Username atau password salah.');
        }

        // ── Login berhasil ──────────────────────────────
        // Simpan informasi admin di session (tersimpan selama browser tidak ditutup)
        session([
            'admin_id'       => $admin->id_admin,   // Dipakai middleware CekLogin
            'admin_username' => $admin->username,    // Ditampilkan di sidebar
        ]);

        // Perbarui session ID untuk keamanan (mencegah session fixation attack)
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Selamat datang, ' . $admin->username . '!');
    }

    /**
     * Logout admin
     * Route: POST /logout
     */
    public function logout(Request $request)
    {
        // Hapus semua data session
        $request->session()->flush();

        // Kembali ke halaman login
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
