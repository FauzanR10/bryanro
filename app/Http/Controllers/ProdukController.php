<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\DetailPesanan;

/**
 * ProdukController
 * ================
 * Controller untuk halaman Data Produk.
 * Mengelola CRUD produk yang dijual di depot BryanRO.
 *
 * Catatan: Saat ini BryanRO hanya punya 1 produk (Galon Isi Ulang),
 * tapi sistem dirancang untuk bisa menampung lebih banyak produk.
 */
class ProdukController extends Controller
{
    /**
     * Tampilkan daftar semua produk
     * Route: GET /produk
     */
    public function index()
    {
        // Ambil semua produk
        $produk = Produk::all();

        // Hitung statistik untuk setiap produk
        $produkDenganStats = $produk->map(function ($p) {
            // Total jumlah terjual dari tabel detail_pesanan
            $totalTerjual = DetailPesanan::where('id_produk', $p->id_produk)->sum('jumlah');
            // Total pendapatan dari produk ini
            $totalPendapatan = DetailPesanan::where('id_produk', $p->id_produk)->sum('subtotal');

            // Tambahkan atribut baru ke objek produk
            $p->total_terjual   = $totalTerjual;
            $p->total_pendapatan = $totalPendapatan;
            return $p;
        });

        return view('produk.index', compact('produkDenganStats'));
    }

    /**
     * Simpan produk baru
     * Route: POST /produk
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:40|unique:produk,nama_produk',
            'harga'       => 'required|numeric|min:0',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.unique'   => 'Nama produk ini sudah ada.',
            'nama_produk.max'      => 'Nama produk maksimal 40 karakter.',
            'harga.required'       => 'Harga wajib diisi.',
            'harga.numeric'        => 'Harga harus berupa angka.',
            'harga.min'            => 'Harga tidak boleh negatif.',
        ]);

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'harga'       => $request->harga,
        ]);

        return redirect()->route('produk.index')
            ->with('success', 'Produk ' . $request->nama_produk . ' berhasil ditambahkan.');
    }

    /**
     * Update data produk (terutama harga)
     * Route: PUT /produk/{id}
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string|max:40|unique:produk,nama_produk,' . $id . ',id_produk',
            'harga'       => 'required|numeric|min:0',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.unique'   => 'Nama produk ini sudah dipakai produk lain.',
            'harga.required'       => 'Harga wajib diisi.',
            'harga.numeric'        => 'Harga harus berupa angka.',
        ]);

        $produk->update([
            'nama_produk' => $request->nama_produk,
            'harga'       => $request->harga,
        ]);

        return redirect()->route('produk.index')
            ->with('success', 'Produk ' . $request->nama_produk . ' berhasil diperbarui.');
    }

    /**
     * Hapus produk
     * Route: DELETE /produk/{id}
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Cek apakah produk pernah dipakai di detail_pesanan
        if (DetailPesanan::where('id_produk', $id)->exists()) {
            return redirect()->route('produk.index')
                ->with('error', 'Produk ' . $produk->nama_produk . ' tidak bisa dihapus karena sudah ada di riwayat pesanan.');
        }

        $nama = $produk->nama_produk;
        $produk->delete();

        return redirect()->route('produk.index')
            ->with('success', 'Produk ' . $nama . ' berhasil dihapus.');
    }
}
