<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model DetailPesanan
 * ===================
 * Merepresentasikan tabel `detail_pesanan`.
 * Tabel ini menyimpan produk apa saja yang ada di dalam satu pesanan.
 * (Satu pesanan bisa punya lebih dari satu jenis produk)
 *
 * Struktur tabel `detail_pesanan`:
 * - id_detail  : Primary key
 * - id_pesanan : FK ke tabel pesanan
 * - id_produk  : FK ke tabel produk
 * - jumlah     : Berapa banyak produk yang dipesan
 * - subtotal   : jumlah × harga produk
 */
class DetailPesanan extends Model
{
    protected $table      = 'detail_pesanan';
    protected $primaryKey = 'id_detail';
    public    $timestamps = false;

    protected $fillable = ['id_pesanan', 'id_produk', 'jumlah', 'subtotal'];

    protected $casts = ['subtotal' => 'float'];

    /**
     * Relasi ke produk
     * Penggunaan: $detail->produk->nama_produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Relasi ke pesanan induk
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
