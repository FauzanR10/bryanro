<?php

namespace App\Services;

/**
 * WhatsAppService
 * ===============
 * Service ini menangani pengiriman pesan WhatsApp otomatis via Fonnte API.
 *
 * Fonnte adalah platform WhatsApp Gateway yang memungkinkan aplikasi
 * mengirim pesan WhatsApp secara otomatis menggunakan nomor WA yang sudah
 * terhubung ke akun Fonnte.
 *
 * Cara setup Fonnte:
 * 1. Daftar di https://fonnte.com/
 * 2. Hubungkan nomor WhatsApp (scan QR code)
 * 3. Salin TOKEN dari dashboard Fonnte
 * 4. Isi FONNTE_TOKEN di file .env aplikasi ini
 *
 * Dokumentasi Fonnte API: https://fonnte.com/docs
 */
class WhatsAppService
{
    // URL endpoint Fonnte API untuk mengirim pesan
    private string $apiUrl = 'https://api.fonnte.com/send';

    // Token autentikasi Fonnte (diambil dari .env)
    private string $token;

    public function __construct()
    {
        // Ambil token dari konfigurasi (file .env → key FONNTE_TOKEN)
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Kirim pesan WhatsApp ke nomor tertentu
     *
     * @param string $nomorWa  Nomor WA penerima (format: 081234567890 atau 6281234567890)
     * @param string $pesan    Isi pesan yang akan dikirim
     * @return array           Hasil pengiriman ['success' => bool, 'message' => string]
     */
    public function kirimPesan(string $nomorWa, string $pesan): array
    {
        // Jika token kosong → skip pengiriman (mode development tanpa Fonnte)
        if (empty($this->token) || $this->token === 'ISI_TOKEN_FONNTE_DISINI') {
            // Log di storage/logs/laravel.log untuk debugging
            \Log::info('[WhatsApp SKIPPED] Token Fonnte kosong. Pesan yang seharusnya dikirim ke ' . $nomorWa . ': ' . $pesan);
            return [
                'success' => false,
                'message' => 'Token Fonnte belum diisi. Cek FONNTE_TOKEN di file .env'
            ];
        }

        // ── Format nomor WhatsApp ─────────────────────
        // Hapus semua karakter selain angka
        $nomor = preg_replace('/[^0-9]/', '', $nomorWa);

        // Ubah awalan 0 menjadi 62 (kode negara Indonesia)
        // Contoh: 081234567890 → 6281234567890
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        // Jika belum ada 62 di awal, tambahkan
        if (!str_starts_with($nomor, '62')) {
            $nomor = '62' . $nomor;
        }

        try {
            // ── Kirim HTTP POST ke Fonnte API ─────────
            // Menggunakan cURL (sudah tersedia di PHP)
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => $this->apiUrl,
                CURLOPT_RETURNTRANSFER => true,        // Kembalikan response sebagai string
                CURLOPT_POST           => true,         // Method POST
                CURLOPT_POSTFIELDS     => [
                    'target'      => $nomor,            // Nomor WA tujuan
                    'message'     => $pesan,            // Isi pesan
                    'countryCode' => '62',              // Kode negara Indonesia
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->token,  // Token autentikasi Fonnte
                ],
                CURLOPT_TIMEOUT => 30,                  // Timeout 30 detik
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Cek apakah ada error koneksi
            if ($curlError) {
                return ['success' => false, 'message' => 'cURL Error: ' . $curlError];
            }

            // Parse response JSON dari Fonnte
            $data = json_decode($response, true);

            // Fonnte mengembalikan {"status": true} jika berhasil
            if ($httpCode === 200 && isset($data['status']) && $data['status'] === true) {
                \Log::info('[WhatsApp SENT] Pesan terkirim ke ' . $nomor);
                return ['success' => true, 'message' => 'Pesan WhatsApp berhasil dikirim ke ' . $nomor];
            }

            \Log::warning('[WhatsApp FAILED] HTTP ' . $httpCode . ' | Response: ' . $response);
            return ['success' => false, 'message' => 'Gagal mengirim: ' . ($data['reason'] ?? $response)];

        } catch (\Exception $e) {
            \Log::error('[WhatsApp ERROR] ' . $e->getMessage());
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Template pesan untuk pesanan BARU
     * Dikirim saat admin menambahkan pesanan pelanggan
     */
    public function templatePesananBaru(
        string $namaPelanggan,
        int    $idPesanan,
        string $namaProduk,
        int    $jumlah,
        float  $total
    ): string {
        $totalFormat = 'Rp ' . number_format($total, 0, ',', '.');

        return "Halo *{$namaPelanggan}*! 👋\n\n"
            . "Pesanan Anda di *Depot BryanRO* telah *diterima*.\n\n"
            . "📋 *Detail Pesanan:*\n"
            . "• No. Pesanan : #{$idPesanan}\n"
            . "• Produk      : {$namaProduk}\n"
            . "• Jumlah      : {$jumlah} galon\n"
            . "• Total       : {$totalFormat}\n\n"
            . "Status saat ini: *Pending* ⏳\n"
            . "Kami akan segera memproses pesanan Anda.\n\n"
            . "Terima kasih telah memesan! 🙏\n"
            . "_— Tim BryanRO_";
    }

    /**
     * Template pesan untuk UPDATE STATUS pesanan
     * Dikirim saat admin mengubah status pesanan (Proses / Selesai)
     */
    public function templateUpdateStatus(
        string $namaPelanggan,
        int    $idPesanan,
        string $statusBaru
    ): string {
        // Pilih emoji dan keterangan berdasarkan status baru
        [$emoji, $keterangan] = match ($statusBaru) {
            'Proses'  => ['🚚', 'Pesanan Anda sedang dalam proses pengiriman.'],
            'Selesai' => ['✅', 'Pesanan Anda telah selesai diantar. Terima kasih sudah memesan di BryanRO!'],
            default   => ['⏳', 'Status pesanan Anda telah diperbarui.'],
        };

        return "Halo *{$namaPelanggan}*! {$emoji}\n\n"
            . "Status pesanan *#{$idPesanan}* telah diperbarui menjadi *{$statusBaru}*.\n\n"
            . "{$keterangan}\n\n"
            . "_— Tim BryanRO_";
    }
}
