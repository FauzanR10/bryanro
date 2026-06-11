<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Produk
 * ============
 * Merepresentasikan tabel `produk` di database.
 *
 * Struktur tabel `produk`:
 * - id_produk   : Primary key (auto increment)
 * - nama_produk : Nama produk (contoh: "Galon Isi Ulang")
 * - harga       : Harga per satuan (decimal 12,2)
 */
class Produk extends Model
{
    protected $table      = 'produk';
    protected $primaryKey = 'id_produk';
    public    $timestamps = false;

    protected $fillable = ['nama_produk', 'harga'];

    // Cast kolom harga menjadi float supaya bisa dioperasikan matematika
    protected $casts = ['harga' => 'float'];

    /**
     * Relasi: Satu produk bisa ada di banyak detail_pesanan
     */
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk', 'id_produk');
    }
}
