<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Admin
 * ===========
 * Merepresentasikan tabel `admin` di database.
 * Tabel ini menyimpan akun admin yang bisa login ke sistem BryanRO.
 *
 * Struktur tabel `admin`:
 * - id_admin  : Primary key (auto increment)
 * - username  : Nama pengguna untuk login
 * - password  : Password yang sudah di-hash dengan bcrypt (varchar 60)
 */
class Admin extends Model
{
    // Nama tabel di database (default Laravel adalah nama model + 's', jadi perlu ditentukan manual)
    protected $table = 'admin';

    // Primary key tabel (default Laravel adalah 'id')
    protected $primaryKey = 'id_admin';

    // Matikan timestamps (tabel tidak punya kolom created_at / updated_at)
    public $timestamps = false;

    // Kolom yang boleh diisi secara massal (mass assignment)
    protected $fillable = ['username', 'password'];

    // Sembunyikan password saat model dikonversi ke array/JSON
    protected $hidden = ['password'];
}
