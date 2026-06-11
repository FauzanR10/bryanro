<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Pesanan
 * =============
 * Merepresentasikan tabel `pesanan` di database.
 * Tabel ini adalah inti dari sistem — menyimpan semua transaksi pesanan.
 *
 * Struktur tabel `pesanan`:
 * - id_pesanan    : Primary key (auto increment)
 * - id_admin      : FK ke tabel admin (siapa yang input pesanan)
 * - id_pelanggan  : FK ke tabel pelanggan
 * - tgl_pesanan   : Tanggal pesanan dibuat
 * - total_harga   : Total harga pesanan
 * - status_pesanan: Status pesanan (enum: 'Pending', 'Proses', 'Selesai')
 */
class Pesanan extends Model
{
    protected $table      = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public    $timestamps = false;

    protected $fillable = [
        'id_admin', 'id_pelanggan', 'tgl_pesanan', 'total_harga', 'status_pesanan'
    ];

    protected $casts = [
        'total_harga'  => 'float',
        'tgl_pesanan'  => 'date',
    ];

    /**
     * Relasi ke tabel pelanggan
     * Penggunaan: $pesanan->pelanggan->nama
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Relasi ke tabel admin
     * Penggunaan: $pesanan->admin->username
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    /**
     * Relasi ke tabel detail_pesanan (produk yang dipesan)
     * Penggunaan: $pesanan->detail → koleksi detail pesanan
     */
    public function detail()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    /**
     * Relasi ke tabel notifikasi_wa
     */
    public function notifikasi()
    {
        return $this->hasMany(NotifikasiWa::class, 'id_pesanan', 'id_pesanan');
    }
}
