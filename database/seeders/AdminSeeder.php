<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder
 * ===========
 * Seeder untuk membuat akun admin pertama di database.
 *
 * Cara menjalankan:
 *   php artisan db:seed --class=AdminSeeder
 *
 * Atau masukkan data secara manual via phpMyAdmin:
 *   INSERT INTO admin (username, password)
 *   VALUES ('admin', '<hasil bcrypt>');
 *
 * Untuk generate hash bcrypt secara manual, jalankan:
 *   php artisan tinker
 *   >>> Hash::make('password123')
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah sudah ada admin, jika belum buat satu
        if (DB::table('admin')->count() === 0) {
            DB::table('admin')->insert([
                'username' => 'admin',
                // Hash::make() membuat hash bcrypt dari password plain text
                // Password yang bisa dipakai login: admin123
                'password' => Hash::make('admin123'),
            ]);

            $this->command->info('✅ Akun admin berhasil dibuat!');
            $this->command->info('   Username : admin');
            $this->command->info('   Password : admin123');
            $this->command->warn('   ⚠️  Segera ganti password setelah login pertama!');
        } else {
            $this->command->warn('⚠️  Akun admin sudah ada, seeder dilewati.');
        }
    }
}
