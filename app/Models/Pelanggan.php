<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Pelanggan
 * ===============
 * Merepresentasikan tabel `pelanggan` di database.
 *
 * Struktur tabel `pelanggan`:
 * - id_pelanggan : Primary key (auto increment)
 * - nama         : Nama lengkap pelanggan
 * - alamat       : Alamat lengkap pelanggan
 * - no_wa        : Nomor WhatsApp pelanggan (UNIQUE — 1 nomor hanya bisa 1 akun)
 */
class Pelanggan extends Model
{
    protected $table      = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public    $timestamps = false;

    protected $fillable = ['nama', 'alamat', 'no_wa'];

    /**
     * Relasi: Satu pelanggan bisa punya banyak pesanan
     * Digunakan: $pelanggan->pesanan → daftar semua pesanan pelanggan ini
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
