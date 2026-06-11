<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;

/**
 * PelangganController
 * ====================
 * Controller untuk halaman Daftar Pelanggan.
 * Mengelola operasi CRUD (Create, Read, Update, Delete) data pelanggan.
 */
class PelangganController extends Controller
{
    /**
     * Tampilkan daftar semua pelanggan
     * Route: GET /pelanggan
     *
     * Fitur: pencarian berdasarkan nama atau nomor WA
     */
    public function index(Request $request)
    {
        // Query dasar: ambil semua pelanggan
        $query = Pelanggan::query();

        // Jika ada parameter pencarian (?cari=xxx), filter hasilnya
        if ($request->filled('cari')) {
            $kata = $request->cari;
            $query->where(function ($q) use ($kata) {
                $q->where('nama', 'like', "%{$kata}%")      // Cari di kolom nama
                  ->orWhere('no_wa', 'like', "%{$kata}%")   // Atau di nomor WA
                  ->orWhere('alamat', 'like', "%{$kata}%");  // Atau di alamat
            });
        }

        // Urutkan berdasarkan nama, tampilkan 10 per halaman (pagination)
        $pelanggan = $query->orderBy('nama')->paginate(10)->withQueryString();

        // Total pelanggan (untuk ditampilkan di kartu statistik)
        $totalPelanggan = Pelanggan::count();

        return view('pelanggan.index', compact('pelanggan', 'totalPelanggan'));
    }

    /**
     * Simpan pelanggan baru ke database
     * Route: POST /pelanggan
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama'   => 'required|string|max:70',
            'alamat' => 'required|string|max:150',
            // no_wa harus unik di tabel pelanggan (satu nomor WA = satu akun)
            'no_wa'  => 'required|string|max:20|unique:pelanggan,no_wa',
        ], [
            'nama.required'   => 'Nama pelanggan wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_wa.required'  => 'Nomor WhatsApp wajib diisi.',
            'no_wa.unique'    => 'Nomor WhatsApp ini sudah terdaftar untuk pelanggan lain.',
            'no_wa.max'       => 'Nomor WhatsApp maksimal 20 karakter.',
        ]);

        // Simpan data pelanggan baru ke database
        Pelanggan::create([
            'nama'   => $request->nama,
            'alamat' => $request->alamat,
            'no_wa'  => $request->no_wa,
        ]);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan ' . $request->nama . ' berhasil ditambahkan.');
    }

    /**
     * Update data pelanggan yang sudah ada
     * Route: PUT /pelanggan/{id}
     */
    public function update(Request $request, $id)
    {
        // Cari pelanggan, tampilkan 404 jika tidak ditemukan
        $pelanggan = Pelanggan::findOrFail($id);

        // Validasi: no_wa harus unik, tapi kecualikan pelanggan yang sedang diedit
        $request->validate([
            'nama'   => 'required|string|max:70',
            'alamat' => 'required|string|max:150',
            // ignore:{id} → abaikan record dengan id ini saat cek unique
            'no_wa'  => 'required|string|max:20|unique:pelanggan,no_wa,' . $id . ',id_pelanggan',
        ], [
            'nama.required'   => 'Nama pelanggan wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_wa.required'  => 'Nomor WhatsApp wajib diisi.',
            'no_wa.unique'    => 'Nomor WhatsApp ini sudah dipakai pelanggan lain.',
        ]);

        // Update data pelanggan
        $pelanggan->update([
            'nama'   => $request->nama,
            'alamat' => $request->alamat,
            'no_wa'  => $request->no_wa,
        ]);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan ' . $request->nama . ' berhasil diperbarui.');
    }

    /**
     * Hapus pelanggan dari database
     * Route: DELETE /pelanggan/{id}
     */
    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        // Cek apakah pelanggan punya pesanan aktif sebelum dihapus
        if ($pelanggan->pesanan()->count() > 0) {
            return redirect()->route('pelanggan.index')
                ->with('error', 'Pelanggan ' . $pelanggan->nama . ' tidak bisa dihapus karena masih punya riwayat pesanan.');
        }

        $nama = $pelanggan->nama;
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan ' . $nama . ' berhasil dihapus.');
    }
}
