<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model NotifikasiWa
 * ==================
 * Merepresentasikan tabel `notifikasi_wa`.
 * Tabel ini mencatat riwayat semua pesan WhatsApp yang dikirim oleh sistem.
 *
 * Setiap kali sistem mengirim WA (saat pesanan dibuat atau status diupdate),
 * hasilnya dicatat di sini untuk keperluan audit/monitoring.
 *
 * Struktur tabel `notifikasi_wa`:
 * - id_notif     : Primary key
 * - id_pesanan   : FK ke tabel pesanan
 * - isi_pesan    : Isi pesan WhatsApp yang dikirim
 * - tgl_kirim    : Tanggal pengiriman
 * - status_kirim : Enum 'Proses' (gagal/pending) atau 'Selesai' (berhasil)
 */
class NotifikasiWa extends Model
{
    protected $table      = 'notifikasi_wa';
    protected $primaryKey = 'id_notif';
    public    $timestamps = false;

    protected $fillable = ['id_pesanan', 'isi_pesan', 'tgl_kirim', 'status_kirim'];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
